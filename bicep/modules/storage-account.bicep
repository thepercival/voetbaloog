@minLength(3)
@maxLength(24)
param name string

@allowed([
  'Standard_LRS'
  'Standard_GRS'
  'Standard_RAGRS'
  'Standard_ZRS'
  'Premium_LRS'
  'Premium_ZRS'
  'Standard_GZRS'
  'Standard_RAGZRS'
])
param sku string

param location string = resourceGroup().location

resource stg 'Microsoft.Storage/storageAccounts@2021-04-01' = {
  name: name
  location: location
  sku: {
    name: sku
  }
  kind: 'StorageV2'
  properties: {
    supportsHttpsTrafficOnly: true
  }
}

// output storageEndpoint object = stg.properties.primaryEndpoints
