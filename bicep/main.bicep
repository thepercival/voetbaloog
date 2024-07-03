param storagePrefix string
// 'examplestg1'

var uniqueStorageName = '${storagePrefix}${uniqueString(resourceGroup().id)}'

module modStorageAccount 'modules/storage-account.bicep' = {
  name: 'storageDeploy'
  params: {
    name: uniqueStorageName
  }
}

output storageEndpoint object = modStorageAccount.outputs.storageEndpoint
