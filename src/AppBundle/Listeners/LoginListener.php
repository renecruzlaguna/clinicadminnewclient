<?php
/**
 * Created by PhpStorm.
 * User: Rene
 * Date: 28/01/2018
 * Time: 16:23
 */

namespace AppBundle\Listeners;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use  \Symfony\Component\Routing\RouterInterface;

class LoginListener implements \Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request $request
     * @param TokenInterface $token
     *
     * @return Response never null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        return new RedirectResponse($this->router->generate('manage'));
        $clientResponse = $request->request->get('g-recaptcha-response');
        if (!($clientResponse)) {
            throw new AuthenticationException('Captcha incorrecto');
        }



        if (!$this->verify($request)) {
            throw new AuthenticationException('Captcha incorrecto');
        }
        return new RedirectResponse($this->router->generate('manage'));

    }

    public function verify(Request $request){

        $clientResponse = $request->request->get('g-recaptcha-response');
        $post_data = http_build_query(
            array(
                'secret' => '6Ld3-EIUAAAAAEz6wPG_2W07tmseNjIgBINqiMJr',
                'response' => $clientResponse

            )
        );
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $post_data
            )
        );
        $context = stream_context_create($opts);
        $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        $result = json_decode($response);
        
        if (!$result->success) {
           return false;

        }
        return true;

    }
}
