<?php
namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        // ...
        $content = '<h1>Oops! An Error Occurred</h1><h2>The server returned a "404 Not Found".</h2>
                    <div>Something is broken. Please let us know what you were doing when this error occurred. 
                    We will fix it as soon as possible. Sorry for any inconvenience caused.</div>';
        return new Response($content, 403);
    }
}