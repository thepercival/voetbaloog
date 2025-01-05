param cosmosAccount object
param name string
param database object

@description('Maximum autoscale throughput for the database shared with up to 25 collections')
@minValue(1000)
@maxValue(1000000)
param sharedAutoscaleMaxThroughput int = 1000

resource resCosmosDbAccount 'Microsoft.DocumentDB/databaseAccounts@2024-05-15' = {
  name: name
  tags: cosmosAccount.tags
  kind: cosmosAccount.kind
  properties: {
    backupPolicy: cosmosAccount.backupPolicy
    databaseAccountOfferType: 'Standard'
    locations: [
      {
        failoverPriority: 0
        isZoneRedundant: false
        locationName: resourceGroup().location
      }
    ]
  }
}

resource resDatabase 'Microsoft.DocumentDB/databaseAccounts/mongodbDatabases@2022-05-15' = {
  parent: resCosmosDbAccount
  name: database.name
  properties: {
    resource: {
      id: database.name
    }
    options: {
      autoscaleSettings: {
        maxThroughput: sharedAutoscaleMaxThroughput
      }
    }
  }
}


resource resCollections 'Microsoft.DocumentDB/databaseAccounts/mongodbDatabases/collections@2023-11-15' = [for collection in database.collections: {
  parent: resDatabase
  name: '${collection.name}'
  properties: {
    resource: {
      id: collection.id
      shardKey: { _id: 'Hash' }
      indexes: [ 
        { key: { keys: [ collection.indexKey ] } } 
      ]
    }
  }
}]
