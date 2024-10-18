param storageAccount object
param logWorkspace object
param appServicePlan object
param website object

module modStorageAccount 'modules/storage-account.bicep' = {
  name: 'storageDeploy'
  params: {
    name: storageAccount.name
    sku: storageAccount.sku
  }
}

module modLogWorkspace 'modules/log-analytics-workspace.bicep' = {
  name: 'logWorkspace'
  params: {
    workspaceName: logWorkspace.name
    sku: logWorkspace.sku
    retentionInDays: logWorkspace.retentionInDays
    resourcePermissions: logWorkspace.resourcePermissions
    heartbeatTableRetention: logWorkspace.heartbeatTableRetention
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

