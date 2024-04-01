# **Magento 2.0 Keep Contacts Extension** #


## Description ##

The standard Magento contact form emails the request to a specified address and does not keep a copy of the enquiries that have been sent form the frontend.

The module allows you to keep and display messages on the administration interface in the Content / Keep Contacts section after the original Contact form has been submitted by the customer. This administration page (grid) shows all received and stored contact messages. It displays all contact information, the sender name, email, telephone and comment.

Every single stored contact messages can be viewed by the administrator. The extension allows to send answer from the admin panel to the customer by opening the selected row in the grid. If the administrator sends an answer, it will be emailed automatically to the customer. The extension comes with a custom email template, which can be loaded and modified in the transactional emails section.

## Features ##

- Module enable/disable.
- Enable modify original contact information.
- Automatic storage and display messages.
- Add Bcc email.
- Include contact comment and information in the answer.
- Send answer email from admin panel.
- Custom email template.
- Multistore support.
- Supported languages: English. 
 
It is a separate module that does not change the default Magento files. 
 
Support: 
Magento Community Edition  2.0.x

Magento Enterprise Edition  2.0.x

## Installation ##

** Important! Always install and test the extension in your development environment, and not on your live or production server. **
 
1. Backup Your Data 
Backup your store database and web directory. 
 
2. Clear Cache and cookies 
Clear the store cache under var/cache and all cookies for your store domain. 
 
3. Disable Compilation 
Disable Compilation, if it’s enabled.

4. Upload Files 
Unzip extension contents on your computer and navigate inside the extracted folder. Create folder app / code on your webserver if you don't have it already. Using your FTP client upload content of the directory to your store root / app / code folder.

5. Enable extension
Please use the following commands in the /bin directory of your Magento 2.0 instance:

    php magento module:enable Me_Econtacts

    php magento setup:upgrade 

One more time clear the cache under var/cache and var/page_cache login to Magento backend (admin panel). In case you have already been logged in during the installation, logout and login back. 

## Configuration ##
 
Login to Magento backend (admin panel).  You can find the module configuration here: Stores / Configuration, in the left menu Magevolve Extensions / Keep Contacts.

Settings:

** Basic **

Enable Extension: Here you can enable the extension.

Enable modify contact: If you choose YES, the administrator will be able to modify the original contact information. 
 
** Email Options **

Email Sender: Please select the sender email address form the default configuration.

Enable Bcc: Enable send answer email to Bcc address.

Email Bcc (if Bcc enabled): Answer will be sent as Bcc to this email address.

Email Template: Email template chosen based on theme fallback when "Default" option is selected.

Include Contact Comment: Please select YES, if you want to include original contact comment in the answer.

Include Contact Information: Please select YES, if you want to include original contact information in the answer.

** Manage Contacts **
 
You can find the Keep Contacts grid under the Content section in the admin panel. This admin grid contains all received contacts.

** Answer / Edit Contact **
	 
Please click on the row or the Answer link in the admin grid. On the next page you can edit the contact, and save it. Also you are able to send your answer email by filling out the "Answer" section and clicking on the Send Answer button. In this case the answer email will be sent and the contact will be saved as well.

## Troubleshooting ##
 
1. After the extension installation I receive a 404 error in Stores / Configuration / Keep Contacts. 
Clear the store cache, browser cookies, logout from backend and login back. 
 
2. My configuration changes do not appear on the store.
Clear the store cache, clear your browser cache and domain cookies and refresh the page. 
 
## Extension license ##
 
The module license description included in the Terms and Conditions:  
http://magevolve.com/terms-and-conditions  
 
## Support ##
 
If you have any questions about the extension, please contact us:
 
E-mail: info@magevolve.com

Monday - Friday, 9am - 5pm CET

## License ##

See COPYING.txt for license details.

Copyright © 2016 Magevolve Ltd. All rights reserved.