<template lang="pug">
select(
  @input="event => $emit('input', event.target.value)"
  :value='value || null'
)
  option(
    v-if='placeholder'
    value=''
    disabled
    selected
    hidden
  ) {{ placeholder }}
  option(
    v-for='option in options'
    :value='option.value || option'
  ) {{ option.label || option }}
</template>

<script>
export default {
  props: ['value', 'options', 'placeholder']
}
</script>
