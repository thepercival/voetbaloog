
// pipeline parameters
param environment sys.string

// parameters.json, add resprov storage, web
param storageAccount object
param keyVault object
param mongoDb object
param appServicePlan object
param website object

var keyVaultName = '${keyVault.name}-${environment}'

module modStorageAccount 'modules/storage-account.bicep' = {
  name: 'storage'
  params: {
    name: '${storageAccount.name}${environment}'
    sku: storageAccount.sku
  }
}

resource resKeyVault 'Microsoft.KeyVault/vaults@2023-07-01' existing = {     
  name: keyVaultName
  scope: resourceGroup(keyVault.resourceGroup)     
}   

module modMongoDb 'modules/mongodb.bicep' = {
  name: 'monogDb'
  params: {
    location: resourceGroup().location
    clusterName: mongoDb.clusterName
    adminUsername: 'localadmin'
    adminPassword: resKeyVault.getSecret(mongoDb.adminPasswordSecretName)
  }
}


module modAppServicePlan 'modules/app-serviceplan.bicep' = {
  name: 'appserviceplan'
  params: {
    name: appServicePlan.name
    sku: appServicePlan.sku
  }
}

module modWebsite 'modules/website.bicep' = {
  name: 'website'
  params: {
    name: '${website.name}-${environment}'
    appServicePlanId: modAppServicePlan.outputs.id
  }
}
