# db-PoC
🔧 Step 1: Set App Settings in Azure
1️⃣ Go to your App Service in the Azure Portal.
2️⃣ Under Settings → Configuration → Application settings, add the following:

Name	Value
* DB_SERVER	tcp:ntmsdb11.database.windows.net,1433
* DB_NAME	db
* DB_USER	sqladmin
* DB_PASSWORD	password
