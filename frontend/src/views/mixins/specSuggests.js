import { specs } from '@/defines/consts'

export default {
  data() {
    return {
      specs
    }
  },
  methods: {
    querySearchSpecs(queryString, cb) {
      const suggests = this.specs.map(spec => {
        return { value: spec.key, label: spec.display_name }
      })

      const results = queryString
        ? suggests.filter(suggest => suggest.label.toLowerCase().indexOf(queryString.toLowerCase()) >= 0)
        : suggests

      cb(results)
    }
  }
}
