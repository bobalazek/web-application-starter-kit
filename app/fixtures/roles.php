<?php

return array(
    // id, name, description, role, priority
    array( 1, 'User', 'The default user role', 'ROLE_USER', 0 ),
    array( 2, 'Super Administrator', 'The role for super administrators', 'ROLE_SUPER_ADMIN', 9999 ),
    array( 3, 'Administrator', 'The role for administrators', 'ROLE_ADMIN', 9000 ),
    array( 4, 'Users Editor', 'The role for the users, that are able to edit the users', 'ROLE_USERS_EDITOR', 5000 ),
    array( 5, 'Roles Editor', 'The role for the users, that are able to edit the roles', 'ROLE_ROLES_EDITOR', 5000 ),
    array( 6, 'Posts Editor', 'The role for the users, that are able to edit the posts', 'ROLE_POSTS_EDITOR', 5000 ),
);
