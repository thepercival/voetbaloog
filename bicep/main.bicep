param storageAccount object

var uniqueStorageName = '${storageAccount.prefix}${uniqueString(resourceGroup().id)}'

module modStorageAccount 'modules/storage-account.bicep' = {
  name: 'storageDeploy'
  params: {
    name: uniqueStorageName
    sku: storageAccount.sku
  }
}

output storageEndpoint object = modStorageAccount.outputs.storageEndpoint
