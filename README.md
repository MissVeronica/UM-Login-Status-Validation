# UM-Login-Status-Validation
## Functions added/changed
Additional tests for user_id valid format integer and not empty.

Clear of the user UM cache and refresh loading of cache added.

Account_status test for status = 'approved' added.

All other status values except pre-defined UM values are rejected login.

A filter hook can be used for custom account_status validation.

New error exit "forbidden" added for invalid user_id with default error text "An error has been encountered" and URL err=forbidden.

Invalid account_status values also with default error text "An error has been encountered" and URL err='status value'.

## Installation 
Install to your child-theme’s functions.php file or
use the “Code Snippets” plugin:

https://wordpress.org/plugins/code-snippets/
