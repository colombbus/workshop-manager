<template lang="pug">
.workshop-survey
  h1.workshop-survey-title déclarer un atelier Declick
  form.workshop-survey-form
    .workshop-survey-tip
      | Tous les champs sont requis sauf mention contraire.
    .workshop-survey-row
      .workshop-survey-label-box
        label.workshop-survey-label(for='workshop-survey-input--animator-code')
          | Quel est votre code animateur ?
      .workshop-survey-input-box
        input(
          v-model.trim='fields.animatorCode'
          type='text'
          placeholder='code animateur'
          id='workshop-survey-input--animator-code'
        )
        .workshop-survey-error(v-if='fieldsErrors.animatorCodeError')
          | {{ fieldsErrors.animatorCodeError }}
      .workshop-survey-row
        .workshop-survey-label-box
          label.workshop-survey-label(for='workshop-survey-input--establishment')
            | Dans quel établissement êtes-vous intervenu ?
        .workshop-survey-input-box
          selection-list(
            v-model='fields.establishment'
            :options='allEstablishments'
            placeholder='établissement'
            required
            id='workshop-survey-input--establishment'
          )
          .workshop-survey-error(v-if='fieldsErrors.establishmentError')
            | {{ fieldsErrors.establishmentError }}
      .workshop-survey-row
        .workshop-survey-label-box
          label.workshop-survey-label
            | Sur quel créneau horaire ?
        .workshop-survey-input-box
          area-picker(
            v-model='fields.day'
            :options='allDays'
            name='workshop-survey-input--day'
          )
          .workshop-survey-error(v-if='fieldsErrors.dayError')
            | {{ fieldsErrors.dayError }}
      .workshop-survey-row
        .workshop-survey-label-box
          label.workshop-survey-label Quel jour ?
        .workshop-survey-input-box
          weekday-selector(
            v-model='fields.date'
            :day='fields.day'
          )
          .workshop-survey-error(v-if='fieldsErrors.dateError')
            | {{ fieldsErrors.dateError }}
      .workshop-survey-row
        .workshop-survey-label-box
          label.workshop-survey-label(
            for='workshop-survey-input--participant-count'
          ) Combien y a-t-il eu de participants ?
        .workshop-survey-input-box
          input(
            v-model.number='fields.participantCount'
            type='number'
            placeholder='nombre de participants'
            id='workshop-survey-input--participant-count'
          )
          .workshop-survey-error(v-if='fieldsErrors.participantCountError')
            | {{ fieldsErrors.participantCountError }}
      .workshop-survey-row
        .workshop-survey-label-box
          label.workshop-survey-label(for='workshop-survey-input--activities')
            | Quelles activités les participants ont-ils fait ?
        .workshop-survey-input-box
          textarea(
            v-model.trim='fields.activities'
            placeholder='détails des activités'
            required
            id='workshop-survey-input--activities'
          )
          .workshop-survey-error(v-if='fieldsErrors.activitiesError')
            | {{ fieldsErrors.activitiesError }}
      .workshop-survey-row
        .workshop-survey-label-box
          label.workshop-survey-label(
            for='workshop-survey-input--appreciation-level'
          ) Comment évalueriez-vous l'appréciation des participants ?
        .workshop-survey-input-box
          selection-list(
            v-model='fields.appreciationLevel'
            :options='allAppreciationLevels'
            placeholder='appréciation'
            required
            id='workshop-survey-input--appreciation-level'
          )
          .workshop-survey-error(v-if='fieldsErrors.appreciationLevelError')
            | {{ fieldsErrors.appreciationLevelError }}
      .workshop-survey-row(v-if="fields.appreciationLevel === 'bad'")
        .workshop-survey-label-box
          label.workshop-survey-label(
            for='workshop-survey-input--appreciation-details'
          ) Pour quelles raisons ?
        .workshop-survey-input-box
          textarea(
            v-model.trim='fields.appreciationDetails'
            placeholder="détails de l'appréciation"
            required
            id='workshop-survey-input--appreciation-details'
          )
          .workshop-survey-error(v-if='fieldsErrors.appreciationDetailsError')
            | {{ fieldsErrors.appreciationDetailsError }}
      .workshop-survey-row
        .workshop-survey-label-box
          label.workshop-survey-label(for='workshop-survey-input--has-met-bugs')
            | Avez-vous rencontré des dysfonctionnements avec les outils
            | utilisés ?
        .workshop-survey-input-box
          selection-list(
            v-model='fields.hasMetBugs'
            :options='allHasMetBugsOptions'
            placeholder='dysfonctionnements'
            required
            id='workshop-survey-input--has-met-bugs'
          )
          .workshop-survey-error(v-if='fieldsErrors.hasMetBugsError')
            | {{ fieldsErrors.hasMetBugsError }}
      .workshop-survey-row(v-if="fields.hasMetBugs === 'yes'")
        .workshop-survey-label-box
          label.workshop-survey-label(for='workshop-survey-input--bug-details')
            | Lesquels ?
        .workshop-survey-input-box
          textarea(
            v-model.trim='fields.bugsDetails'
            placeholder='détails des dysfonctionnements'
            required
            id='workshop-survey-input--bug-details'
          )
          .workshop-survey-error(v-if='fieldsErrors.bugsDetailsError')
            | {{ fieldsErrors.bugsDetailsError }}
      .workshop-survey-row
        .workshop-survey-label-box
          label.workshop-survey-label(for='workshop-survey-input--remarks')
            | Avez-vous d'autres remarques à faire ?
        .workshop-survey-input-box
          textarea(
            v-model.trim='fields.remarks'
            placeholder='remarques'
            id='workshop-survey-input--remarks'
          )
          .workshop-survey-error(v-if='fieldsErrors.remarksError')
            | {{ fieldsErrors.remarksError }}
    .workshop-survey-row
      .workshop-survey-submit-box
        button.workshop-survey-submit-button(
          @click='submit'
          v-if="status === 'inactive'"
          type='button'
        )
          span.workshop-survey-submit-icon
          | &nbsp;envoyer
        button.workshop-survey-submit-button(
          v-if="status === 'pending'"
          type='button'
          disabled
        )
          span.workshop-survey-waiting-icon
          | &nbsp;envoi en cours
        button.workshop-survey-submit-button(
          v-if="status === 'done'"
          type='button'
          disabled
        )
          span.workshop-survey-done-icon
          | &nbsp;envoi effectué, merci
        .workshop-survey-error(v-if='globalError')
          | {{ globalError }}
</template>

<script>
import axios from 'axios'
import AreaPicker from '@/components/generic/input/AreaPicker'
import SelectionList from '@/components/generic/input/SelectionList'
import WeekdaySelector from '@/components/generic/input/WeekdaySelector'
import * as fieldsOptions from '@/components/workshop/fields-options'
import { BACK_END_PATH } from '@/config'

export const MISSING_REQUIRED_FIELD = 'Ce champ est requis.'
export const INVALID_VALUES = 'Envoi annulé, le formulaire n\'est pas ' +
  'correctement rempli.'

export default {
  data () {
    return {
      fields: {
        animatorCode: '',
        establishment: null,
        day: null,
        date: null,
        participantCount: null,
        activities: '',
        appreciationLevel: null,
        appreciationDetails: '',
        hasMetBugs: null,
        bugsDetails: '',
        remarks: ''
      },
      fieldsErrors: {
        animatorCodeError: null,
        establishmentError: null,
        dayError: null,
        dateError: null,
        participantCountError: null,
        activitiesError: null,
        appreciationLevelError: null,
        appreciationDetailsError: null,
        hasMetBugsError: null,
        bugsDetailsError: null,
        remarksError: null
      },
      globalError: null,
      status: 'inactive'
    }
  },
  computed: {
    requiredFields () {
      const fields = [
        'animatorCode',
        'establishment',
        'day',
        'date',
        'participantCount',
        'activities',
        'appreciationLevel',
        'hasMetBugs'
      ]
      if (this.fields.appreciationLevel === 'bad') {
        fields.push('appreciationDetails')
      }
      if (this.fields.hasMetBugs === 'yes') {
        fields.push('bugsDetails')
      }
      return fields
    },
    allEstablishments: () => fieldsOptions.allEstablishments,
    allDays: () => fieldsOptions.allDays,
    allAppreciationLevels: () => fieldsOptions.allAppreciationLevels,
    allHasMetBugsOptions: () => fieldsOptions.allHasMetBugsOptions
  },
  watch: {
    fields: {
      handler () {
        for (const field in this.fields) {
          if (this.requiredFields.includes(field)) {
            if (!this.isFieldEmpty(field)) {
              this.clearFieldError(field)
            }
          }
        }
      },
      deep: true
    }
  },
  methods: {
    isFieldEmpty (field) {
      const value = this.fields[field]
      return value === undefined || value === null || value.length === 0
    },
    setFieldError (field, error) {
      this.fieldsErrors[`${field}Error`] = error
    },
    fieldHasError (field) {
      return this.fieldsErrors[`${field}Error`] === null
    },
    clearFieldError (field) {
      this.fieldsErrors[`${field}Error`] = null
    },
    async submit () {
      let isReady = true
      for (const field of this.requiredFields) {
        if (this.isFieldEmpty(field)) {
          this.setFieldError(field, MISSING_REQUIRED_FIELD)
          isReady = false
        }
      }
      if (!isReady) {
        this.globalError = INVALID_VALUES
        return
      }
      this.globalError = null
      this.status = 'pending'
      await axios.post(BACK_END_PATH, this.fields)
      this.status = 'done'
    }
  },
  components: {
    AreaPicker,
    SelectionList,
    WeekdaySelector
  }
}
</script>
