param storageAccount object
param appServicePlan object
param website object

module modStorageAccount 'modules/storage-account.bicep' = {
  name: 'storageDeploy'
  params: {
    name: storageAccount.name
    sku: storageAccount.sku
  }
}

// output storageEndpoint object = modStorageAccount.outputs.storageEndpoint

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
    appServicePlanId: appServicePlan.id
  }
}

// resource srcControls 'Microsoft.Web/sites/sourcecontrols@2021-01-01' = {
//   name: '${appService.name}/web'
//   properties: {
//     repoUrl: repositoryUrl
//     branch: branch
//     isManualIntegration: true
//   }
// }

