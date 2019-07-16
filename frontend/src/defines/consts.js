export const specs = [
  { key: '1KG', display_name: '1KG' },
  { key: '1.25KG', display_name: '1.25KG' },
  { key: '4KG', display_name: '4KG' },
  { key: '5KG', display_name: '5KG' },
  { key: '10KG', display_name: '10KG' },
  { key: '12KG', display_name: '12KG' },
  { key: '14.4KG', display_name: '14.4KG' },
  { key: '15KG', display_name: '15KG' },
  { key: '16KG', display_name: '16KG' },
  { key: '18KG', display_name: '18KG' },
  { key: '20KG', display_name: '20KG' },
  { key: '24KG', display_name: '24KG' },
  { key: '25KG', display_name: '25KG' },
  { key: '4加仑', display_name: '4加仑' },
  { key: '500ML', display_name: '500ML' },
  { key: '12L', display_name: '12L' },
  { key: '16L', display_name: '16L' }
]

// arr to obj, such as { g_1kg : "1kg/罐", x_20kg : "20kg/箱" }
export const specsKeyValue = specs.reduce((acc, cur) => {
  acc[cur.key] = cur.display_name
  return acc
}, {})

export const RECYCLABLE_TYPES = [
  { value: 'bucket', label: '桶' },
  { value: 'box', label: '纸箱' }
]

export const QC_TYPES = [
  { value: 'IQC', label: 'IQC' },
  { value: 'SC', label: '生产部' }
]
