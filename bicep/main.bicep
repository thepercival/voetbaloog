param storageAccount object
param appServicePlan object
param website object

output storageAccountName string = storageAccount.name
output appServicePlanName string = appServicePlan.name
output websiteName string = website.name

// module modStorageAccount 'modules/storage-account.bicep' = {
//   name: 'storageDeploy'
//   params: {
//     name: storageAccount.name
//     sku: storageAccount.sku
//   }
// }

// module modAppServicePlan 'modules/app-serviceplan.bicep' = {
//   name: 'appserviceplan'
//   params: {
//     name: appServicePlan.name
//     sku: appServicePlan.sku
//   }
// }

// module modWebsite 'modules/website.bicep' = {
//   name: 'website'
//   params: {
//     name: website.name
//     appServicePlanId: modAppServicePlan.outputs.id
//   }
// }

