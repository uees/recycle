export function EnteringWarehouse() {
  return {
    id: undefined,
    recyclable_type: undefined,
    product_name: undefined,
    product_batch: undefined,
    spec: undefined,
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
    recyclable_type: undefined,
    customer_id: undefined,
    created_user_id: undefined,
    product_name: undefined,
    product_batch: undefined,
    weight: undefined,
    amount: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function RecycledThing() {
  return {
    id: undefined,
    recyclable_type: undefined,
    customer_id: undefined,
    confirmed_user_id: undefined,
    amount: undefined,
    confirmed_amount: undefined,
    recycled_user: undefined,
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
    type: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function Customer() {
  return {
    id: undefined,
    name: undefined,
    address: undefined,
    salesman: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function User() {
  return {
    id: undefined,
    phone: undefined,
    email: undefined,
    name: undefined,
    avatar: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function Role() {
  return {
    id: undefined,
    name: undefined,
    display_name: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}
