<?php

namespace AppBundle\EventListener;

use AppBundle\Controller\SwordController;
use AppBundle\Exception\SwordException;
use Monolog\Logger;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Listen for exceptions in the SWORD controller, and produce an error document.
 */
class SwordExceptionListener {
    
    /**
     * @var TwigEngine
     */
    private $templating;
    
    /**
     * @var callable
     */
    private $controller;
    
    /**
     * @var Logger
     */
    private $logger;
	
	/**
	 * @var RequestStack
	 */
	private $requestStack;

    /**
     * Set the logger for exceptions
     *
     * @param Logger $logger
     */
    public function setLogger(Logger $logger) {
        $this->logger = $logger;
    }

    /**
     * Set the Twig Engine for templating and output.
     * 
     * @param TwigEngine $templating
     */
    public function setTemplating(TwigEngine $templating) {
        $this->templating = $templating;
    }

    /**
     * Respond to an exception with an error document wrapped in a Response.
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event) {
        $exception = $event->getException();

        // only intercept SwordController exceptions.
        if( ! $this->controller[0] instanceof SwordController) {
            return;
        }
		
		if( ! $exception instanceof SwordException) {
			return;
		}

        $this->logger->critical($exception->getMessage() . ' from ' . $this->requestStack->getCurrentRequest()->getClientIp());
//        $this->logger->critical($exception->getTraceAsString());
        
        if($exception instanceof SwordException) {
            $response = $this->templating->renderResponse(
                    'AppBundle:Sword:error.xml.twig', 
                    array( 'error' => $exception)
            );
            $response->headers->set('Content-Type', 'text/xml');
            $response->setStatusCode($exception->getStatusCode());
            $event->setResponse($response);
        }
    }
    
    /**
     * Once the controller has been initialized, this event is fired. Grab
     * a reference to the active controller.
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $this->controller = $event->getController();
    }
	
	public function setRequestStack(RequestStack $requestStack) {
		$this->requestStack = $requestStack;
	}
}