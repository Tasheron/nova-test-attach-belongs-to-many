<template>
  <modal :show="open" class="flex flex-row items-center h-full">
    <div class="container bg-white p-6 rounded dark:bg-gray-800">
      <slot />
      <div class="mt-6 flex justify-end gap-4">
        <button type="button" class="h-9 px-3 bg-primary-500 text-white rounded float-right font-bold" @click="$emit('confirm')">Confirm</button>
        <button type="button" class="h-9 px-3 bg-red-500 text-white rounded float-right font-bold" @click="$emit('close')">Close</button>
      </div>
    </div>
  </modal>
</template>

<script>
export default {
  props: {
    open: {
      type: Boolean,
      required: true,
    },
  },
}
</script>