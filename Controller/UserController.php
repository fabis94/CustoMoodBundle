<?php
namespace CustoMood\Bundle\AppBundle\Controller;

use CustoMood\Bundle\AppBundle\Entity\User;
use CustoMood\Bundle\AppBundle\Form\ChangeEmailType;
use CustoMood\Bundle\AppBundle\Form\ChangePasswordType;
use CustoMood\Bundle\AppBundle\Form\UserType;
use CustoMood\Bundle\AppBundle\Model\ChangeEmail;
use CustoMood\Bundle\AppBundle\Model\ChangePassword;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="user_index")
     */
    public function indexAction(Request $request)
    {
        return $this->render('CustoMoodAppBundle::User/index.html.twig');
    }

    /**
     * @Route("/edit", name="user_edit")
     */
    public function editAction(Request $request)
    {
        /** @var User $sessionUser */
        $sessionUser = $this->getUser();
        $changePassModel = new ChangePassword();
        $changePassForm = $this->createForm(ChangePasswordType::class, $changePassModel);

        $changeEmailModel = new ChangeEmail();
        $changeEmailModel->setNewEmail($sessionUser->getEmail());
        $changeEmailForm = $this->createForm(ChangeEmailType::class, $changeEmailModel);

        // Handle form submits
        $changeEmailForm->handleRequest($request);
        $changePassForm->handleRequest($request);

        if ($changeEmailForm->isSubmitted() && $changeEmailForm->isValid()) {
            // Change email
            $em = $this->getDoctrine()->getManager();
            $sessionUser->setEmail($changeEmailModel->getNewEmail());
            $em->flush();

            // Show success
            $this->addFlash(
                'success',
                'Email successfully changed!'
            );
        } else if ($changePassForm->isSubmitted() && $changePassForm->isValid()) {
            // Change password
            $em = $this->getDoctrine()->getManager();
            $password = $this->get('security.password_encoder')
                ->encodePassword($sessionUser, $changePassModel->getNewPassword());
            $sessionUser->setPassword($password);
            $em->flush();

            // Show success
            $this->addFlash(
                'success',
                'Password successfully changed!'
            );
        }

        return $this->render('CustoMoodAppBundle::User/edit.html.twig', array(
            'user_email_form' => $changeEmailForm->createView(),
            'user_password_form' => $changePassForm->createView()
        ));
    }

    /**
     * @Route("/login", name="user_login")
     */
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('CustoMoodAppBundle::User/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/register", name="user_register")
     */
    public function registerAction(Request $request)
    {
        // Is registration enabled? Have the user registrations reached the capacity?
        $settings = $this->get('customood.settings');
        $registrationEnabled = $settings->get('registration_open');
        $maxUsers = $settings->get('maximum_user_amount');

        $em = $this->getDoctrine()->getManager();
        $userCount = $em->getRepository(User::class)->count();

        if (!$registrationEnabled || $userCount >= $maxUsers) {
            if (!$registrationEnabled) {
                $error_message = 'Registration is currently disabled! Please contact your administrator for more info.';
            } else {
                $error_message = 'Registration is at capacity! Please contact your administrator for more info.';
            }

            $this->addFlash('error', $error_message);
            return $this->render('CustoMoodAppBundle::User/register.html.twig', ['disabled' => true]);
        }

        // Registration enabled - continue
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $em->persist($user);
            $em->flush();

            // Show success
            $this->addFlash(
                'success',
                'Account successfully created, you can now log in!'
            );

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'CustoMoodAppBundle::User/register.html.twig',
            array('form' => $form->createView())
        );
    }
}