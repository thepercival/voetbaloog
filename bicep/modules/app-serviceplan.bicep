param name string
param location string = resourceGroup().location
param sku string

resource resAppServicePlan 'Microsoft.Web/serverfarms@2022-09-01' = {
  name: name
  location: location
  properties: {
    reserved: true
  }
  sku: {
    name: sku
  }
}
