### RUN THIS COMMAND IN PROJECT ROOT DIRECTORY:

```
#Installing COMPOSER.

curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
sudo php /tmp/composer-setup.php --install-dir=/usr/bin --filename=composer

#Installing the AWS SDK for PHP 3.x on our system using COMPOSER.
composer require aws/aws-sdk-php
```

### THE BELOW ARE THE PARAMETERS THAT ARE REQUIRED FOR RDS WHILE UISNG AWS SDK PHP 3.X:

```
1) 'DBInstanceIdentifier' => 'mydbinstance'
2) 'Engine' => 'mysql'
3) 'DBInstanceClass' => 'db.t3.micro'
4) 'MasterUsername' => 'admin'
5) 'MasterUserPassword' => 'wappnet@2023'

   OR

   'ManageMasterUserPassword' => true || false

--> True = Then it will store the password in "aws secret manager".

    = We can get our password from there.

    = But remember to turn off the rotation of the password.

--> false = then no need to use this directly specify above.

6) 'EngineVersion' => '8.0'
7) 'AllocatedStorage' => 20
8) 'PubliclyAccessible' => true || false      //OPTIONAL 
9) 'DeletionProtection' => true || false      //OPTIONAL 
10) 'VpcSecurityGroupIds' => [$securityGroupId]
11) 'DBSubnetGroupName' => 'my-db-subnet-group'
```
