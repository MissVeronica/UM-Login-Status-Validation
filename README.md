# UM Login Status Validation
## Functions added/changed
Additional tests for user_id valid format integer and not empty.

Clear of the user UM cache and refresh loading of cache added.

Account_status test for status = 'approved' added and allowed to login.

All other status values including pre-defined UM values are rejected login.

A filter hook 'um_login_status_validation' can be used for custom account_status validation.

New error exit "forbidden" added for invalid user_id with default error text "An error has been encountered" and URL err=forbidden.

Invalid account_status values also with default error text "An error has been encountered" and URL err='status value'.
### Version 2

Logging of time, user_id and Reject code for the last 10 rejects.

Shortcode [reject_login_log] to list the reject log in the front-end for an Admin.

Parameters example: [reject_login_log meta_keys="country,description,user_registered,user_email"]

Valid user meta-keys comma separated for additional columns to the log listing.

## Installation 
Install the source.php file to your child-theme’s functions.php file or
use the “Code Snippets” plugin:

https://wordpress.org/plugins/code-snippets/

### Version 2
Replace the source.php file with source2.php in your child-theme’s functions.php file or
use the “Code Snippets” plugin.
