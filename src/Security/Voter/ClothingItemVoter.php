<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\ClothingItem;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends Voter<string, ClothingItem>
 */
final class ClothingItemVoter extends Voter
{
    public const string EDIT = 'edit';
    public const string VIEW = 'view';
    public const string DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof ClothingItem;
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token,
        ?Vote $vote = null
    ): bool {

        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            $vote?->addReason('The user must be logged in to access this resource.');

            return false;
        }

        return match ($attribute) {
            self::VIEW => $this->canView($subject, $user, $vote),
            self::EDIT => $this->canEdit($subject, $user, $vote),
            self::DELETE => $this->canDelete($subject, $user, $vote),
            default => false,
        };
    }

    private function canView(
        ClothingItem $clothingItem,
        User $user,
        ?Vote $vote = null
    ): bool {
        // only if the user is the owner of the item
        if ($clothingItem->getOwner() === $user) {
            $vote?->addReason('Own clothing item entry.');

            return true;
        }

        $vote?->addReason('No access to this clothing item.');

        return false;
    }

    private function canEdit(
        ClothingItem $clothingItem,
        User $user,
        ?Vote $vote = null
    ): bool {
        // Only the owner can edit
        if ($clothingItem->getOwner() === $user) {
            $vote?->addReason('Owner can edit.');

            return true;
        }

        $vote?->addReason('Only the owner can edit.');

        return false;
    }

    private function canDelete(
        ClothingItem $clothingItem,
        User $user,
        ?Vote $vote = null
    ): bool {
        // Only the owner can delete
        if ($clothingItem->getOwner() === $user) {
            $vote?->addReason('Owner can delete.');

            return true;
        }

        $vote?->addReason('Only the owner can delete.');

        return false;
    }
}
