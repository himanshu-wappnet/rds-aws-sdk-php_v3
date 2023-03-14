<?php
require 'vendor/autoload.php';

use Aws\Ec2\Ec2Client;
use Aws\Rds\RdsClient;

// Set up the EC2 client
$ec2Client = new Ec2Client([
    'region' => 'ap-south-1',
    'version' => 'latest'
]);

// Create a new VPC
$vpc = $ec2Client->createVpc([
    'CidrBlock' => '10.0.0.0/16',
    'InstanceTenancy' => 'default'
]);

$vpcId = $vpc->get('Vpc')['VpcId'];

echo "Created VPC with ID: $vpcId\n";

// Create a new security group for the RDS instance
$securityGroup = $ec2Client->createSecurityGroup([
    'GroupName' => 'MyRdsSecurityGroup',
    'Description' => 'Security group for RDS instance in MyVPC',
    'VpcId' => $vpcId
]);

$securityGroupId = $securityGroup->get('GroupId');

$ec2Client->authorizeSecurityGroupIngress([
    'GroupId' => $securityGroupId,
    'IpPermissions' => [
        [
            'IpProtocol' => 'tcp',
            'FromPort' => 3306,
            'ToPort' => 3306,
            'IpRanges' => [
                [
                    'CidrIp' => '0.0.0.0/0'
                ]
            ]
        ]
    ]
]);

echo "Created security group with ID: $securityGroupId\n";

    // Create two public subnets in different AZs for the VPC
    $subnet1 = $ec2Client->createSubnet([
        'CidrBlock' => '10.0.0.0/24',
        'VpcId' => $vpcId,
        'AvailabilityZone' => 'ap-south-1a'
    ]);

    $subnet2 = $ec2Client->createSubnet([
        'CidrBlock' => '10.0.1.0/24',
        'VpcId' => $vpcId,
        'AvailabilityZone' => 'ap-south-1b'
    ]);

    $subnetId1 = $subnet1->get('Subnet')['SubnetId'];
    $subnetId2 = $subnet2->get('Subnet')['SubnetId'];

    // Create a new DB subnet group
    $rdsClient = new RdsClient([
        'region' => 'ap-south-1',
        'version' => 'latest'
    ]);

    $dbSubnetGroupName = 'my-db-subnet-group';      // change the subnet group name

    // Create a new DB subnet group with the two subnets
    $rdsClient->createDBSubnetGroup([
        'DBSubnetGroupName' => $dbSubnetGroupName,
        'DBSubnetGroupDescription' => 'Subnet group for RDS instance in MyVPC',
        'SubnetIds' => [$subnetId1, $subnetId2]
    ]);
    
    echo "Created DB subnet group with name: $dbSubnetGroupName and subnet IDs: $subnetId1, $subnetId2\n";


$params = [
    'DBInstanceIdentifier' => 'mydbinstance-new-1',    //change the database name
    'DBInstanceClass' => 'db.t3.micro',
    'Engine' => 'mysql',
    'EngineVersion' => '8.0',
    'MasterUsername' => 'admin',
    'MasterUserPassword' => 'wappnet@2023',
    'AllocatedStorage' => 20,
    'VpcSecurityGroupIds' => [$securityGroupId],
    'DBSubnetGroupName' => $dbSubnetGroupName
];

// Create the RDS instance
$result = $rdsClient->createDBInstance($params);

// Output the RDS instance details
echo 'DB Instance ID: ' . $result['DBInstance']['DBInstanceIdentifier'] . PHP_EOL;
echo 'DB Instance Class: ' . $result['DBInstance']['DBInstanceClass'] . PHP_EOL;
echo 'DB Engine: ' . $result['DBInstance']['Engine'] . PHP_EOL;
echo 'DB Engine Version: ' . $result['DBInstance']['EngineVersion'] . PHP_EOL;
if (isset($result['DBInstance']['Endpoint']['Address'])) {
    echo 'DB Endpoint: ' . $result['DBInstance']['Endpoint']['Address'] . PHP_EOL;
} else {
    echo 'DB Endpoint not yet available.' . PHP_EOL;
}
?>