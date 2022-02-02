# UM Login Status Validation
## Functions added/changed
Additional tests for user_id valid format integer and not empty.

Clear of the user UM cache and refresh loading of cache added.

Account_status test for status = 'approved' added and allowed to login.

All other status values including pre-defined UM values are rejected login.

A filter hook 'um_login_status_validation' can be used for custom account_status validation.

New error exit "forbidden" added for invalid user_id with default error text "An error has been encountered" and URL err=forbidden.

Invalid account_status values also with default error text "An error has been encountered" and URL err='status value'.

## Installation 
Install the source.php file to your child-theme’s functions.php file or
use the “Code Snippets” plugin:

https://wordpress.org/plugins/code-snippets/
## Version 2

Logging of date and time, user_id and Reject code for the last 30 rejects.

Shortcode [reject_login_log] to list the reject log in the front-end for an Admin User.

User Info listed by the shortcode: Date and Time, Rejected code, User ID, Username, Role, Registration date.

Parameters example: 

[reject_login_log meta_keys="mobile_number,user_email,description"]

Valid user meta-keys, comma separated for additional columns to the shortcode log listing.
## Installation 
Replace the source.php file with source2.php in your child-theme’s functions.php file or
the “Code Snippets” plugin.
