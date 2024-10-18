
@description('abbreviations at https://learn.microsoft.com/en-us/azure/cloud-adoption-framework/ready/azure-best-practices/resource-abbreviations')
param workspaceName string

@description('Pricing tier: PerGB2018 or legacy tiers (Free, Standalone, PerNode, Standard or Premium) which are not available to all customers.')
@allowed([
	'CapacityReservation'
  'Free'
  'LACluster'
  'PerGB2018'
  'PerNode'
  'Premium'
  'Standalone'
  'Standard'
])
param sku string

param location string = resourceGroup().location

param retentionInDays int

@description('true to use resource or workspace permissions. false to require workspace permissions.')
param resourcePermissions bool

@description('Number of days to retain data in Heartbeat table.')
param heartbeatTableRetention int

resource workspace 'Microsoft.OperationalInsights/workspaces@2023-09-01' = {
  name: workspaceName
  location: location
  properties: {
    sku: {
      name: sku
    }
    retentionInDays: retentionInDays
    features: {
      enableLogAccessUsingOnlyResourcePermissions: resourcePermissions
    }
  }
}

resource workspaceName_Heartbeat 'Microsoft.OperationalInsights/workspaces/tables@2022-10-01' = {
  parent: workspace
  name: 'Heartbeat'
  properties: {
    retentionInDays: heartbeatTableRetention
  }
}
