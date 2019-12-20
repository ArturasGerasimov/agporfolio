<?php


namespace App\Security;


use App\Entity\Comments;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentSecurityVoter extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';
    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;
    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }
        if (!$subject instanceof Comments) {
            return false;
        }
        return true;
    }
    protected function voteOnAttribute(
        $attribute, $subject, TokenInterface $token
    ) {
//        if ($this->decisionManager->decide($token, [User::ROLE_USER])) {
//            return true;
//        }
        $authenticatedUser = $token->getUser();
        if (!$authenticatedUser instanceof User) {
            return false;
        }
        /** @var Comments $comment */
        $comment = $subject;
        return $comment->getUser()->getId() === $authenticatedUser->getId();
    }
}