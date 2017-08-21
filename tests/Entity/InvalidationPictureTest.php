<?php

namespace App\Tests\Entity;

use App\Checker\InvalidatePictureReason;
use App\Entity\InvalidationPicture;
use App\Entity\Picture;
use App\Entity\Place;
use App\Entity\Travel;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class InvalidationPictureTest extends TestCase
{
    public function testGetUser()
    {
        $user = (new User())
            ->setUsername('username')
            ->setFirstname('firstname')
            ->setLastname('lastname')
        ;

        $invalidationPicture = (new InvalidationPicture())
            ->setPicture((new Picture())->setTravel((new Travel())->setUser($user)))
        ;

        $this->assertSame('username (firstname lastname)', $invalidationPicture->getUser());
    }

    public function testGetTravel()
    {
        $travel = (new Travel())
            ->setTitle('title')
            ->setPlace((new Place())->setTitle('place'))
            ->setStart(new \DateTime('2017-01-02'))
            ->setEnd(new \DateTime('2017-03-04'))
        ;

        $invalidationPicture = (new InvalidationPicture())
            ->setPicture((new Picture())->setTravel($travel))
        ;

        $this->assertSame('title (place 02/01/2017-04/03/2017)', $invalidationPicture->getTravel());
    }

    public function testGetPlace()
    {
        $invalidationPicture = (new InvalidationPicture())
            ->setPicture((new Picture())->setPlace((new Place())->setTitle('place')))
        ;

        $this->assertSame('place', $invalidationPicture->getPlace());
    }

    public function testCommentIsRequiredWithReasonOthers()
    {
        $constraint = $this->createMock(ConstraintViolationBuilderInterface::class);
        $constraint
            ->expects($this->once())
            ->method('addViolation')
            ->withAnyParameters()
        ;

        $constraint
            ->expects($this->once())
            ->method('atPath')
            ->with('comment')
            ->willReturn($constraint)
        ;

        $context = $this->createMock(ExecutionContextInterface::class);
        $context
            ->expects($this->once())
            ->method('buildViolation')
            ->with('invalidation_picture.comment_required')
            ->willReturn($constraint)
        ;

        $invalidationPicture = (new InvalidationPicture())->setReason(InvalidatePictureReason::OTHERS);

        $invalidationPicture->commentIsRequiredWithReasonOthers($context, null);
    }

    public function testCommentIsRequiredWithReasonOthersButReasonNotOthers()
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $context
            ->expects($this->never())
            ->method('buildViolation')
        ;

        $invalidationPicture = (new InvalidationPicture())->setReason('reason');

        $invalidationPicture->commentIsRequiredWithReasonOthers($context, null);
    }

    public function testCommentIsRequiredWithReasonOthersButCommentNotEmpty()
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $context
            ->expects($this->never())
            ->method('buildViolation')
        ;

        $invalidationPicture = (new InvalidationPicture())
            ->setReason(InvalidatePictureReason::OTHERS)
            ->setComment('comment')
        ;

        $invalidationPicture->commentIsRequiredWithReasonOthers($context, null);
    }
}
