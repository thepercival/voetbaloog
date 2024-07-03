param storageAccount object

module modStorageAccount 'modules/storage-account.bicep' = {
  name: 'storageDeploy'
  params: {
    name: storageAccount.name
    sku: storageAccount.sku
  }
}

output storageEndpoint object = modStorageAccount.outputs.storageEndpoint
