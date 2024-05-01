# **Magento 2 Keep Contacts Extension** #


## Description ##

The standard Magento contact form emails the request to a specified address and does not keep a copy of the enquiries that have been sent form the frontend.

The module allows you to keep and display messages on the administration interface in the Content / Keep Contacts section after the original Contact form has been submitted by the customer. This administration page (grid) shows all received and stored contact messages. It displays all contact information, the sender name, email, telephone and comment.

Every single stored contact messages can be viewed by the administrator. The extension allows to send answer from the admin panel to the customer by opening the selected row in the grid. If the administrator sends an answer, it will be emailed automatically to the customer. The extension comes with a custom email template, which can be loaded and modified in the transactional emails section.

## Features ##

- Module enable/disable.
- Automatic storage and display messages.
- Add Cc email.
- Include contact comment and information in the answer.
- Send answer email from admin panel.
- Custom email template.
- Multistore support.
- Supported languages: English. 
 
It is a separate module that does not change the default Magento files. 
 
Support: 
- Magento Community Edition  2.4.x

- Adobe Commerce 2.4.x

## Installation ##

** Important! Always install and test the extension in your development environment, and not on your live or production server. **
 
1. Backup Your Data
   Backup your store database and whole Magento 2 directory.

2. Enable extension
   Please use the following commands in your Magento 2 console:

   ```
   bin/magento module:enable Space_KeepContacts

   php magento setup:upgrade 
   ```

## Configuration ##
 
Login to Magento backend (admin panel).  You can find the module configuration here: Stores / Configuration, in the left menu Space Extensions / Keep Contacts.

Settings:

### Configuration ###

Enable Extension: Here you can enable the extension.
 
### Email Options ###

Include Original Comment: Please select YES, if you want to include original contact comment in the answer.

Email Sender: Please select the sender email address form the default configuration.

Email CC Emails To: Please enter the cc copy email address where notification will be also sent.

Email Template: Email template chosen based on theme fallback when "Default" option is selected.

** Manage Contacts **
 
You can find the Keep Contacts grid under the Marketing section in the admin panel. This admin grid contains all received contacts.

** Answer / Edit Contact **
	 
Please click on the row or the Edit/Answer link in the admin grid. On the next page you can edit the contact, and save it. Also, you are able to send your answer email by filling out the "Answer" section and clicking on the Save & Answer button. In this case the answer email will be sent and the contact will be saved as well.

## Change Log ##

Version 1.0.0 - May 1, 2024
- Compatibility with Magento Community Edition  2.4.x

- Compatibility with Adobe Commerce 2.4.x
 
## Support ##

If you have any questions about the extension, please contact with me.

## License ##

MIT License.