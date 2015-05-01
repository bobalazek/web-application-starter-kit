<?php

namespace Application\Test\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;

class RoleTypeTest
    extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'name' => 'Admin',
            'description' => 'The role for admins',
            'role' => 'ROLE_ADMIN',
        );

        $type = new \Application\Form\Type\RoleType();
        $entity = new \Application\Entity\RoleEntity();

        $form = $this->factory->create(
            $type,
            $entity
        );

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($entity, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
