<?php

namespace CustoMood\Bundle\AppBundle\Service;

class AnalysisService
{
    /**
     * Validate the format of the returned data
     * @param $data
     * @return bool
     */
    public function validateReturnedData($data)
    {
        if ($data == null || !is_array($data))
            return false;

        foreach ($data as $dataItem) {
            if (!is_array($dataItem) || count($dataItem) != 2 ||
                !array_key_exists('text', $dataItem) ||
                !array_key_exists('date', $dataItem))
                return false;
        }

        return true;
    }

    /**
     * Analyze data
     * @param $data
     * @param $aggregatePeriod
     * @return array
     */
    public function analyzeData($data, $aggregatePeriod)
    {
        $labels = [];
        $values = [];

        // Order data by time (ascending)
        usort($data, [$this, 'sortDataByTimeFunc']);
        $aggregatePeriodSeconds = $aggregatePeriod * 86400;

        // TEST: Add dates
        foreach ($data as &$dataTestItem) {
            $testDate = \DateTimeImmutable::createFromFormat('U', $dataTestItem['date']);
            $dataTestItem['dateReal'] = $testDate;
        }

        $dataIndex = 0;
        $dataCount = count($data);
        for ($dateFrom = $data[0]['date']; $dateFrom <= $data[$dataCount-1]['date']; $dateFrom += $aggregatePeriodSeconds) {
            $dateTo = $dateFrom + $aggregatePeriodSeconds;
            $dateFromTest = \DateTimeImmutable::createFromFormat('U', $dateFrom);
            $dateToTest = \DateTimeImmutable::createFromFormat('U', $dateTo);
            $aggregateScore = 0;

            $anyDataInThisPeriod = false;
            for ($i = $dataIndex; $i < $dataCount && $data[$i]['date'] < $dateTo; $i++) {
                $dataItem = $data[$i];
                $timestamp = $dataItem['date'];
                $score = $this->getScore($dataItem['text']);
                $aggregateScore += $score;
                $anyDataInThisPeriod = true;
            }
            $dataIndex = $i;

            $date = \DateTime::createFromFormat('U', $dateFrom);
            $labels[] = $date->format('d-m-Y');
            $values[] = $anyDataInThisPeriod ? $aggregateScore : null;

            if ($dataCount <= $dataIndex)
                break;
        }

        // Simplify score to [-1;+1]
        $this->simplifyScores($values);

        return [$labels, $values];
    }

    /**
     * @param $scores
     */
    protected function simplifyScores(&$scores)
    {
        $cleanScores = array_filter($scores); // Filter cleans out nulls so min/max can be measured accurately
        $min = min($cleanScores); // Array filter cleans out nulls
        $max = max($cleanScores);
        $realMax = max($max, abs($min));

        foreach ($scores as &$score) {
            $score = $score != null ? round($score / $realMax, 2) : null;
        }
    }

    /**
     * @param $itemA
     * @param $itemB
     * @return bool
     */
    protected function sortDataByTimeFunc($itemA, $itemB)
    {
        return $itemA['date'] > $itemB['date'];
    }

    protected function getScore($text)
    {
        $positives = $this->positivePhrases();
        $negatives = $this->negativePhrases();
        $score = 0;

        foreach ($positives as $moodPhrase) {
            if (strlen($text) < 1)
                break;

            $freq = 0;
            $text = str_replace($moodPhrase['phrase'], '', $text, $freq);
            if ($freq > 0) {
                $score += $freq * $moodPhrase['weight'];
            }
        }

        foreach ($negatives as $moodPhrase) {
            if (strlen($text) < 1)
                break;

            $freq = 0;
            $text = str_replace($moodPhrase['phrase'], '', $text, $freq);
            if ($freq > 0) {
                $score += $freq * $moodPhrase['weight'];
            }
        }

        return $score;
    }

    protected function getScoreOld($text)
    {
        $phrasesData = $this->getPhraseFrequencies($text);
        return $this->getScoreFromPhraseData($phrasesData);
    }

    protected function getScoreFromPhraseData($phrasesData)
    {
        $score = 0;
        $positives = $this->positivePhrases();
        $negatives = $this->negativePhrases();
        $phrases = $phrasesData['phrases'];
        $freqs = $phrasesData['freqs'];

        foreach ($positives as $positive) {
            if (in_array($positive['phrase'], $phrases)) {
                $score += $positive['weight'] * $freqs[$positive['phrase']];
            }
        }

        foreach ($negatives as $negative) {
            if (in_array($negative['phrase'], $phrases)) {
                $score += $negative['weight'] * $freqs[$negative['phrase']];
            }
        }

        return $score;
    }

    /**
     * @param $text
     * @return array
     */
    protected function getPhraseFrequencies($text)
    {
        $phrases = [];
        $freqs = [];

        // Tokenize string
        $tokens = $this->tokenizeString($text);
        $tokenCount = count($tokens);
        if ($tokenCount > 0) {
            for ($i = 0; $i < $tokenCount; $i++) {
                for ($j = 0; $j < min(5, $tokenCount - $i); $j++) {
                    $phrase = implode(" ", array_slice($tokens, $i, $j +1));
                    $phrase = strtolower($phrase);

                    if (isset($freqs[$phrase])) {
                        $freqs[$phrase]++;
                    } else {
                        $phrases[] = $phrase;
                        $freqs[$phrase] = 1;
                    }

//                    $phrases[] = $phrase;
//                    $freqs[$phrase] = isset($freqs[$phrase]) ? $freqs[$phrase] + 1 : 1;
                }
            }
        }

        $t = 1;

        return [
            'phrases' => $phrases,
            'freqs' => $freqs
        ];
    }

    /**
     * Tokenize string
     * @param $string
     * @return array
     */
    protected function tokenizeString($string)
    {
        $tokens = null;
        $tokenCount = preg_match_all('/([\w]+|[^\w\s]+)/', $string, $tokens);
        if ($tokenCount) {
            return $tokens[0];
        }

        return [];
    }

    /**
     * Positive phrases
     * @return array
     */
    protected function positivePhrases()
    {
        $phrases = [
            ['phrase' => "hello", 'weight' => 10],
            ['phrase' => "for advice", 'weight' => 20],
            ['phrase' => "sorted out", 'weight' => 20],
            ['phrase' => "can you please", 'weight' => 10],
            ['phrase' => "thanks!", 'weight' => 20],
            ['phrase' => "hi", 'weight' => 5],
            ['phrase' => "please", 'weight' => 10],
            ['phrase' => "can you", 'weight' => 5],
            ['phrase' => "please help", 'weight' => 10],
            ['phrase' => "wonderful", 'weight' => 30],
            ['phrase' => "so quick", 'weight' => 20],
            ['phrase' => "so quickly", 'weight' => 20],
            ['phrase' => "very happy", 'weight' => 20],
            ['phrase' => "happy with", 'weight' => 20],
            ['phrase' => "great work", 'weight' => 20],
            ['phrase' => "!!", 'weight' => 10],
            ['phrase' => "!!!", 'weight' => 13],
            ['phrase' => "!!!!", 'weight' => 15],
            ['phrase' => "please advise", 'weight' => 10],
            ['phrase' => "advise please", 'weight' => 10],
            ['phrase' => "advise", 'weight' => 10],
            ['phrase' => "kind regards", 'weight' => 10],
            ['phrase' => "please proceed", 'weight' => 10],
            ['phrase' => "proceed with", 'weight' => 10],
            ['phrase' => "however", 'weight' => 1],
            ['phrase' => "advise", 'weight' => 10],
            ['phrase' => "brilliant", 'weight' => 20],
            ['phrase' => "very good", 'weight' => 10],
            ['phrase' => "am happy", 'weight' => 10],
            ['phrase' => "re happy", 'weight' => 10],
            ['phrase' => "are happy", 'weight' => 10],
            ['phrase' => "happy", 'weight' => 5],
            ['phrase' => "good solution", 'weight' => 10],
            ['phrase' => "thanks", 'weight' => 10],
            ['phrase' => "thank", 'weight' => 10],
            ['phrase' => "thank you", 'weight' => 20],
            ['phrase' => "many thanks", 'weight' => 30],
            ['phrase' => "thanks for", 'weight' => 20],
            ['phrase' => "looks great", 'weight' => 20],
            ['phrase' => "is great", 'weight' => 10],
            ['phrase' => "great", 'weight' => 5],
            ['phrase' => "good work", 'weight' => 20],
            ['phrase' => "good job", 'weight' => 10],
            ['phrase' => "great job", 'weight' => 20],
            ['phrase' => "well done", 'weight' => 20],
            ['phrase' => "it works", 'weight' => 5],
            ['phrase' => "no problem", 'weight' => 15],
            ['phrase' => "appreciate", 'weight' => 5],
            ['phrase' => "wow", 'weight' => 20],
            ['phrase' => "awesome", 'weight' => 10],
            ['phrase' => "enjoy", 'weight' => 10],
            ['phrase' => "all good", 'weight' => 20],
            ['phrase' => ":)", 'weight' => 15],
            ['phrase' => ":-)", 'weight' => 15]
        ];

        usort($phrases, [$this, 'sortPhraseArrayFunc']);
        return $phrases;
    }

    /**
     * Negative phrases
     * @return array
     */
    protected function negativePhrases()
    {
        $phrases = [
            ['phrase' => "delay", 'weight' => -1],
            ['phrase' => "how is it going", 'weight' => -1],
            ['phrase' => "any news", 'weight' => -10],
            ['phrase' => "when are you", 'weight' => -5],
            ['phrase' => "please explain", 'weight' => -1],
            ['phrase' => "please update", 'weight' => -5],
            ['phrase' => "provide update", 'weight' => -5],
            ['phrase' => "any update", 'weight' => -10],
            ['phrase' => "awaiting", 'weight' => -5],
            ['phrase' => "do about", 'weight' => -5],
            ['phrase' => "problem right now", 'weight' => -5],
            ['phrase' => "get back to", 'weight' => -5],
            ['phrase' => "confused", 'weight' => -5],
            ['phrase' => "is the status", 'weight' => -1],
            ['phrase' => "can we", 'weight' => -1],
            ['phrase' => "what was wrong", 'weight' => -10],
            ['phrase' => "fixed", 'weight' => -10],
            ['phrase' => "dont see", 'weight' => -10],
            ['phrase' => "do not see", 'weight' => -10],
            ['phrase' => "??", 'weight' => -10],
            ['phrase' => "???", 'weight' => -20],
            ['phrase' => "not sure", 'weight' => -10],
            ['phrase' => "make sure", 'weight' => -1],
            ['phrase' => "it does not", 'weight' => -30],
            ['phrase' => "does not", 'weight' => -1],
            ['phrase' => "error message", 'weight' => -1],
            ['phrase' => "confused", 'weight' => -10],
            ['phrase' => "is going on", 'weight' => -20],
            ['phrase' => "still", 'weight' => -20],
            ['phrase' => "seem to", 'weight' => -10],
            ['phrase' => "any updates", 'weight' => -10],
            ['phrase' => "my bad", 'weight' => -1],
            ['phrase' => "bad", 'weight' => -10],
            ['phrase' => "previous comment", 'weight' => -10],
            ['phrase' => "comment above", 'weight' => -10],
            ['phrase' => "comment below", 'weight' => -10],
            ['phrase' => "last comment", 'weight' => -10],
            ['phrase' => "cannot see", 'weight' => -10],
            ['phrase' => "not working", 'weight' => -10],
            ['phrase' => "issues", 'weight' => -15],
            ['phrase' => "...", 'weight' => -10],
            ['phrase' => "can this", 'weight' => -5],
            ['phrase' => "urgent", 'weight' => -5],
            ['phrase' => "understand", 'weight' => -15],
            ['phrase' => "unable to", 'weight' => -5],
            ['phrase' => "too long", 'weight' => -30],
            ['phrase' => "cannot", 'weight' => -20],
            ['phrase' => "problem", 'weight' => -10],
            ['phrase' => "catastrophic", 'weight' => -50],
            ['phrase' => "asap", 'weight' => -30],
            ['phrase' => "missing", 'weight' => -30],
            ['phrase' => "disappointed", 'weight' => -50]
        ];

        usort($phrases, [$this, 'sortPhraseArrayFunc']);
        return $phrases;
    }

    protected function sortPhraseArrayFunc($phraseItemA, $phraseItemB)
    {
        return strlen($phraseItemA['phrase']) < strlen($phraseItemB['phrase']);
    }
}