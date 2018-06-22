<template lang='pug'>
.weekday-selector
  button.weekday-selector-button(@click='selectPreviousWeek' type='button')
    span.weekday-selector-previous-icon
  span.weekday-selector-date {{ date.format('LL') }}
  button.weekday-selector-button(@click='selectNextWeek' type='button')
    span.weekday-selector-next-icon
</template>

<script>
import moment from 'moment'
import 'moment/locale/fr'

moment.locale('fr')

export default {
  props: ['day'],
  data () {
    return {
      now: moment(),
      weekOffset: 0
    }
  },
  computed: {
    date () {
      const value = this.now
        .clone()
        .add(this.weekOffset, 'w')
        .day(this.day || 0)
      return value.day() > this.now.day()
        ? value.subtract(1, 'w')
        : value
    }
  },
  watch: {
    date () {
      this.$emit('input', this.date.format('LL'))
    }
  },
  methods: {
    selectPreviousWeek () {
      this.weekOffset--
    },
    selectNextWeek () {
      this.weekOffset++
    }
  }
}
</script>
