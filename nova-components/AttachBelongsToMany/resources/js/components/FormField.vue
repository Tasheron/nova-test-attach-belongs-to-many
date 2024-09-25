<template>
  <DefaultField
    :field="field"
    :errors="errors"
    :show-help-text="showHelpText"
    :full-width-content="fullWidthContent"
  >
    <template #field>
      <input
        :id="field.attribute"
        type="text"
        class="w-full form-control form-input form-control-bordered"
        :placeholder="'Search ' + field.attribute"
        @input="findObjects"
      />
      <ul
        v-if="findList.length"
        class="w-full text-sm font-medium text-grey-100 border border-gray-200 rounded dark:border-gray-600"
      >
        <li class="w-full flex cursor-default rounded-t select-none border-b border-gray-200 bg-gray-100 dark:border-gray-600 dark:bg-gray-800">
          <div class="py-2 flex-none w-14 text-center border-r border-gray-200 dark:border-gray-600">ID</div>
          <div class="py-2 px-4 flex-1">Name</div>
        </li>
        <li
          :id="item.id"
          class="w-full flex cursor-pointer dark:bg-gray-700"
          :class="{
            'border-b border-gray-200 dark:border-gray-600': index != findList.length - 1,
            'rounded-b': index == findList.length - 1,
          }"
          @click="attach(item)"
          v-for="(item, index) in findList"
        >
          <div class="py-2 flex-none w-14 text-center border-r border-gray-200 dark:border-gray-600">{{ item.id }}</div>
          <div class="py-2 px-4 flex-1">{{ item[field.nameField] }}</div>
        </li>
      </ul>
      <div class="w-full mt-2 text-sm font-medium text-grey-100 border border-gray-200 bg-gray-100 rounded flex flex-row select-none cursor-default dark:border-gray-600 dark:bg-gray-800">
        <div class="py-2 flex-none w-14 text-center border-r border-gray-200 dark:border-gray-600">#</div>
        <div class="py-2 flex-none w-14 text-center border-r border-gray-200 dark:border-gray-600">ID</div>
        <div class="px-4 py-2 flex-1">Name</div>
      </div>
      <draggable :list="value" @end="changeIndex">
        <div
          :id="resource.id"
          class="w-full mt-2 text-sm font-medium text-grey-100 border border-gray-200 rounded flex flex-row cursor-move select-none dark:border-gray-600 dark:bg-gray-700"
          v-for="(resource, index) in value"
        >
          <div class="py-2 flex-none w-14 text-center border-r border-gray-200 dark:border-gray-600">{{ index + 1 }}</div>
          <div class="py-2 flex-none w-14 text-center border-r border-gray-200 dark:border-gray-600">{{ resource.id }}</div>
          <div class="px-4 py-2 flex-1">{{ resource[field.nameField] }}</div>
          <button class="w-12 flex-none bg-primary-500 text-white"
            @click.prevent="editPivot(resource)"
          ><Icons type="edit" class="mt-1" /></button>
          <button class="w-12 flex-none bg-red-500 text-white rounded-r"
            @click.prevent="detach(resource.id)"
          ><Icons type="trash-2" class="mt-1" /></button>
        </div>
      </draggable>
      <PivotModal :open="showPivotPopup" @close="showPivotPopup = false" @confirm="confirmPivot">
        <div 
          class="w-full flex flex-row gap-6"
          :class="{'mt-2': index != 0}"
          v-for="(pivot, index) in field.pivotFields"
        >
          <div class="py-2 text-left capitalize">
            {{ pivot.name }}
          </div>
          <input
            :id="pivot.uniqueKey"
            :data-name="pivot.attribute"
            :type="pivot.type"
            :value="pivotResource.pivot[pivot.attribute]"
            class="form-control form-input form-control-bordered capitalize"
            :placeholder="pivot.name"
          />
        </div>
      </PivotModal>
    </template>
  </DefaultField>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'
import VueFeather from 'vue-feather';
import { VueDraggableNext } from 'vue-draggable-next'
import PivotModal from './PivotModal.vue';

export default {
  mixins: [FormField, HandlesValidationErrors],

  props: ['resourceName', 'resourceId', 'field'],

  data() {
    return {
      findList: [],
      showPivotPopup: false,
      pivotResource: null,
    }
  },

  components: {
    Icons: VueFeather,
    Draggable: VueDraggableNext,
    PivotModal
  },

  methods: {
    setInitialValue() {
      this.value = this.field.value || '';
    },

    fill(formData) {
      formData.append(this.fieldAttribute, this.value || '')
    },

    findObjects(e) {
      let val = e.target.value;

      if (val != '') {
        this.$http.post(
          `/api/${this.field.attachApiResourceName}/find`,
          {
            filter: val,
            resourceId: this.resourceId,
          }
        ).then(response => this.findList = response.data)
          .catch(() => Nova.$toasted.error('Error with searching resource'));
      } else {
        this.findList = [];
      }
    },

    detach(id) {
      this.$http.delete(
        `/api/${this.field.mainApiResourceName}/${this.resourceId}/${this.field.attachApiResourceName}/${id}`
      ).then(() => {
        this.value = this.value.filter(el => el.id != id);
        Nova.$toasted.success('Model detach successfully');
      }).catch(() => Nova.$toasted.error(`Error when detaching resource with id ${id}`));
    },

    attach(item) {
      this.$http.put(
        `/api/${this.field.mainApiResourceName}/${this.resourceId}/${this.field.attachApiResourceName}/${item.id}`
      ).then(response => {
        this.findList = [];
        this.value.push(response.data);
        this.$el.querySelector(`#${this.field.attribute}`).value = '';
        Nova.$toasted.success('Model attach successfully');
      }).catch(error => Nova.$toasted.error(`Error when attaching resource with id ${item.id}: ${error.message}`));
    },

    changeIndex(e) {
      if (e.oldIndex == e.newIndex) {
        return;
      }

      this.$http.post(
        `/api/${this.field.mainApiResourceName}/${this.resourceId}/changeIndex/${e.clone.id}`,
        {[this.field.sortingField]: e.newIndex}
      ).then(response => {
        this.value = response.data;
        Nova.$toasted.success('Sorting saved successfully');
      }).catch(error => Nova.$toasted.error(`Error when saving sorting: ${error.message}`));
    },

    editPivot(resource) {
      this.showPivotPopup = true;
      this.pivotResource = resource;
    },

    confirmPivot() {
      let newPivotValues = {};
      
      this.field.pivotFields.forEach(pivot => {
        let el = document.querySelector(`#${pivot.uniqueKey}`);

        if (this.pivotResource.pivot[pivot.attribute] != el.value) {
          newPivotValues[el.dataset.name] = el.value;
        }
      });   

      if (Object.keys(newPivotValues).length != 0) {
        this.$http.post(
          `/api/${this.field.mainApiResourceName}/${this.resourceId}/updatePivot/${this.pivotResource.id}`,
          {newValues: newPivotValues}
        ).then(response => {
          if (newPivotValues[this.field.sortingField] != undefined) {
            this.value = response.data;
          }

          Nova.$toasted.success('Pivot values saved successfully');
        }).catch(error => Nova.$toasted.error(`Error with saving pivot: ${error.message}`));
      }

      this.showPivotPopup = false;
    },
  },
}
</script>
