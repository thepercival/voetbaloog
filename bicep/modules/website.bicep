param name string
param appServicePlanId string

resource appService 'Microsoft.Web/sites@2022-09-01' = {
  name: name
  location: resourceGroup().location
  properties: {
    serverFarmId: appServicePlanId
  }
}
