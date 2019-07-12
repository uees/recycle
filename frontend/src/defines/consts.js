export const specs = [
  { key: 'g_1kg', display_name: '1kg/罐' },
  { key: 'g_4kg', display_name: '1kg/罐' },
  { key: 'g_5kg', display_name: '5kg/罐' },
  { key: 'g_20kg', display_name: '20kg/桶' },
  { key: 'x_10kg', display_name: '10kg/箱' },
  { key: 'x_15kg', display_name: '15kg/组' },
  { key: 'x_18kg', display_name: '18kg/组' },
  { key: 'x_20kg', display_name: '20kg/箱' }
]

// arr to obj, such as { g_1kg : "1kg/罐", x_20kg : "20kg/箱" }
export const specsKeyValue = specs.reduce((acc, cur) => {
  acc[cur.key] = cur.display_name
  return acc
}, {})

export const RECYCLABLE_TYPES = [
  {value: 'bucket', label: '桶'},
  {value: 'box', label: '纸箱'}
]

export const QC_TYPES = [
  {value: 'IQC', label: 'IQC'},
  {value: 'SC', label: '生产部'}
]
