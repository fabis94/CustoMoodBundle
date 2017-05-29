<?php


namespace CustoMood\Bundle\AppBundle\Model;

/***
 * Describes Adapter metadata
 *
 * Class AdapterItem
 * @package CustoMood\Bundle\AppBundle\Model
 */
class AdapterItem
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $displayName;

    /**
     * @var string
     */
    protected $author;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $website;

    /**
     * @var float
     */
    protected $version;

    /**
     * AdapterItem constructor.
     */
    public function __construct()
    {
        $this->version = 0.1;
    }


    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return AdapterItem
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     * @return AdapterItem
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $author
     * @return AdapterItem
     */
    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return AdapterItem
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     * @return AdapterItem
     */
    public function setWebsite($website)
    {
        $this->website = $website;
        return $this;
    }

    /**
     * @return float
     */
    public function getVersion(): float
    {
        return $this->version;
    }

    /**
     * @param float $version
     * @return AdapterItem
     */
    public function setVersion(float $version): AdapterItem
    {
        $this->version = $version;
        return $this;
    }
}