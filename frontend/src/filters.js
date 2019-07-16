import { RECYCLABLE_TYPES } from '@/defines/consts'

export function recyclableType(data) {
  const el = RECYCLABLE_TYPES.find(el => {
    if (el.value === data) {
      return true
    }
  })

  if (el) {
    return el.label
  }
}

export function countBad(records) {
  let amount = 0
  for (const record of records) {
    amount += +record.bad_amount
  }
  return amount
}
