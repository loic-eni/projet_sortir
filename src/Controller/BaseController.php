<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\State;

class BaseController extends AbstractController
{
    const STATE = [
        State::STATE_CREATED,
        State::STATE_OPENED,
        State::STATE_CLOSED,
        State::STATE_ACTIVITY_IN_PROGRESS,
        State::STATE_PASSED,
        State::STATE_CANCELED,
        State::STATE_ARCHIVED
    ];
}
