import RestApi from '../utils/restapi'

export const enteringWarehousesApi = new RestApi({
  url: '/entering-warehouses'
})

export const shipmentsApi = new RestApi({
  url: '/shipments'
})

export const recyclesApi = new RestApi({
  url: '/recycles'
})

export const qcRecordsApi = new RestApi({
  url: '/qc-records'
})

export const customersApi = new RestApi({
  url: '/customers'
})
