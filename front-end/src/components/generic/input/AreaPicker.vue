<template lang="pug">
.area-picker
  template(v-for='(option, index) in options')
    input.area-picker-input(
      type='radio'
      :name='name'
      :value='option.value'
      :checked='option.value == value'
      :id='option.id || null'
    )
    label.area-picker-label(
      @click='select'
      :for='option.id || null'
    ) {{ option.label }}
</template>

<script>
export default {
  props: ['value', 'name', 'options'],
  methods: {
    select (event) {
      const target = event.target.previousElementSibling
      this.$emit('input', target.value)
    }
  }
}
</script>
