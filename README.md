**SEND EMAIL BLAST :**

The class to sent Email is : Logimonde\QuickPresse\Classes;


**SWITCH COMPLETELY TO ELASTIC EMAIL**

STEP #1 : change smtp value on config/mail.php

_____________________________________

**SEND EMAIL TO NEW REGISTERED USER**

namespace Logimonde\Account\Components;
UserRegister.php

Logimonde\Account\assets\assets\js\register.js
public function onCompanyData()
private function sendEmailConfirmation($data, $user)
_______________________________________________________


***SEND test email for campaign***
namespace Logimonde\Quickpresse\Components;
/quickpresse/components/Manage.php

_______________________________________________


***WORKFLOW HOW MASS MAILING SEND AFTER APPROVAL BY NATHALIE***
1- We suppose to have a cron Job on the server to sen email :

* * * * * cd /var/www/quickpresse-com && /usr/bin/php artisan schedule:run

2- The Scheduler function is call from Plugin.php 
   In the scheduler function the SendRegular::send is call
   
   
   registerSchedule()
   
__________________________________________________________

***REMINDER TO GET THE LAST SQL STRUCTURE IN DEV 
LINODE AND IMPORT ONLY BACKUP DATA FROM CDT PROD*****

___________________________________________________________