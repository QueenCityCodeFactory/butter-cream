<?php
declare(strict_types=1);

namespace ButterCream\Error;

use ButterCream\Network\Exception\HttpException;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Error\ExceptionRenderer as CakeExceptionRenderer;
use Cake\Http\Response;
use Cake\Network\Exception\HttpException as CakeHttpException;
use InvalidArgumentException;
use Throwable;

class ExceptionRenderer extends CakeExceptionRenderer
{
    /**
     * Get error message.
     *
     * @param \Throwable $exception Exception.
     * @param int $code Error code.
     * @return string Error message
     */
    protected function _message(Throwable $exception, int $code): string
    {
        $message = $exception->getMessage();

        if (
            Configure::read('debug') !== true &&
            !($exception instanceof HttpException) &&
            !($exception instanceof CakeHttpException) &&
            !($exception instanceof InvalidArgumentException)
        ) {
            if ($exception instanceof RecordNotFoundException) {
                $message = __d('cake', 'Record Not Found');
            } elseif ($code < 500) {
                $message = __d('cake', 'Not Found');
            } else {
                $message = __d('cake', 'An Internal Error Has Occurred.');
            }
        }

        return $message;
    }

    /**
     * A safer way to render error messages, replaces all helpers, with basics
     * and doesn't call component methods.
     *
     * @param string $template The template to render.
     * @return \Cake\Http\Response A response object that can be sent.
     */
    protected function _outputMessageSafe(string $template): Response
    {
        $helpers = ['ButterCream.Form', 'ButterCream.Html', 'ButterCream.Time'];
        $builder = $this->controller->viewBuilder();
        $builder
            ->setHelpers($helpers, false)
            ->setLayoutPath('')
            ->setTemplatePath('Error')
            ->setTheme('ButterCream');
        $view = $this->controller->createView('View');

        $response = $this->controller->getResponse()
            ->withType('html')
            ->withStringBody($view->render($template, 'error'));
        $this->controller->setResponse($response);

        return $response;
    }
}
