export function EnteringWarehouse() {
  return {
    id: undefined,
    product_name: '',
    product_batch: '',
    spec: '',
    weight: undefined,
    amount: undefined,
    entered_at: undefined,
    made_at: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function Shipment() {
  return {
    id: undefined,
    customer_id: undefined,
    created_user_id: undefined,
    product_name: '',
    product_batch: '',
    weight: 0,
    amount: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function RecycledThing() {
  return {
    id: undefined,
    customer_id: undefined,
    confirmed_user_id: undefined,
    amount: undefined,
    confirmed_amount: undefined,
    recycled_user: '',
    confirmed_at: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function QcRecord() {
  return {
    id: undefined,
    recycled_thing_id: undefined,
    bad_amount: undefined,
    type: '',
    created_at: undefined,
    updated_at: undefined
  }
}

export function Customer() {
  return {
    id: undefined,
    name: '',
    address: '',
    salesman: '',
    created_at: undefined,
    updated_at: undefined
  }
}

export function User() {
  return {
    id: undefined,
    phone: '',
    email: '',
    name: '',
    avatar: '',
    created_at: undefined,
    updated_at: undefined
  }
}

export function Role() {
  return {
    id: undefined,
    name: '',
    display_name: '',
    created_at: undefined,
    updated_at: undefined
  }
}
