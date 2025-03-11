<template>
  <div ref="calendar"></div>
</template>

<script>
import { onMounted, ref } from 'vue';
import { Calendar } from '@fullcalendar/core';
import interactionPlugin from '@fullcalendar/interaction';
import dayGridPlugin from '@fullcalendar/daygrid';

export default {
  setup() {
    const calendarRef = ref(null);

    onMounted(() => {
      var calendarEl = calendarRef.value;

      var calendar = new Calendar(calendarEl, {
        plugins: [interactionPlugin, dayGridPlugin],
        initialView: 'dayGridMonth'
      });

      calendar.render();
    });

    return {
      calendarRef
    };
  }
};
</script>

<style scoped>
/* Add any styles for the calendar component */
</style>
