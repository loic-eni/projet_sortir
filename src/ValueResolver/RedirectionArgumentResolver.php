<?php
namespace App\ValueResolver;

use App\DTO\Redirection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RedirectionArgumentResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        // Check if the parameter is of type Redirection
        if ($argument->getType() === Redirection::class) {
            // Get the redirection data from the query string
            $params = $request->query->all();

            if(isset($params['redirection'])) {
                $redirectionParam = $params['redirection'];

                if (!$redirectionParam || !isset($redirectionParam['label']) || !isset($redirectionParam['url'])) {
                    throw new BadRequestHttpException('Missing or invalid redirection parameters.');
                }

                $redirection = new Redirection();
                $redirection->setLabel($redirectionParam['label']);
                $redirection->setUrl($redirectionParam['url']);

                yield $redirection;
            }
        }
    }
}