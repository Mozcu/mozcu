<?php
/**
 * Description of AuthenticationHandler
 *
 * @author mauro
 */

namespace Mozcu\MozcuBundle\Handler;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface,
                                        AuthenticationFailureHandlerInterface,
                                        LogoutSuccessHandlerInterface {
    
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Security\Core\Exception\AuthenticationException $exception
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        if ($request->isXmlHttpRequest()) {
            $response = new Response(json_encode(array('success' => false, 'message' => $exception->getMessage())));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            // Create a flash message with the authentication error message
            $request->getSession()->setFlash('error', $exception->getMessage());
            $url = $this->router->generate('MozcuMozcuBundle_home');

            return new RedirectResponse($url);
        }
    }

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {
        $user = $token->getUser();
        $url = $this->router->generate('MozcuMozcuBundle_profile', array('username' => $user->getUsername()));
        
        if ($request->isXmlHttpRequest()) {
            $response = new Response(json_encode(array('success' => true, 'callback_url' => $url)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            return new RedirectResponse($url);
        }
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function onLogoutSuccess(Request $request)
    {
        $request->getSession()->invalidate();
        if ($request->isXmlHttpRequest()) {
            $url = $this->router->generate('MozcuMozcuBundle_ajaxGetHome');
            $response = new Response(json_encode(array('success' => true, 'callback_url' => $url)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            $url = $this->router->generate('MozcuMozcuBundle_home');
            return new RedirectResponse($url);
        }
    }

}