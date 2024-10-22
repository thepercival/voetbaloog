
// pipeline parameters
param environment sys.string

// parameters.json
param storageAccount object
param appServicePlan object
param website object

module modStorageAccount 'modules/storage-account.bicep' = {
  name: 'storageDeploy'
  params: {
    name: '${storageAccount.name}${environment}'
    sku: storageAccount.sku
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
    name: website.name
    appServicePlanId: modAppServicePlan.outputs.id
  }
}
