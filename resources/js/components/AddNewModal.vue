<template>
  <div
    id="editUserModal"
    tabindex="-1"
    class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-10 justify-center items-center p-4 w-full md:inset-0 h-modal md:h-full flex bg-slate-500 bg-opacity-30"
    aria-modal="true"
    role="dialog"
  >
    <div class="relative w-full max-w-2xl h-full md:h-auto">
      <!-- Modal content -->
      <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
        <!-- Modal header -->
        <div
          class="flex justify-between items-start p-4 rounded-t border-b dark:border-gray-600"
        >
          <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ title }}</h3>
          <button
            type="button"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
            data-modal-toggle="editUserModal"
            @click="$emit('close')"
          >
            <svg
              class="w-5 h-5"
              fill="currentColor"
              viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd"
              ></path>
            </svg>
          </button>
        </div>
        <!-- Modal body -->
        <slot name="body">
          <div class="p-6 space-y-6">
            <div class="flex flex-col space-y-3">
              <div class="col-span-6">
                <label
                  for="user_id"
                  class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                  >User ID</label
                >
                <input
                  type="number"
                  name="user_id"
                  id="user_id"
                  v-model="formData.user_id"
                  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  placeholder="35262"
                  required="required"
                />
              </div>
              <small class="text-red-500 text-left">{{ error.user_id }}</small>
              <div class="col-span-6">
                <label
                  for="file"
                  class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                  >Image</label
                >
                <input
                  type="file"
                  accept="image/png, image/gif, image/jpeg, image/jpg"
                  name="image"
                  v-on:change="fileChange()"
                  id="file"
                  ref="file"
                  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  placeholder="Image"
                  required="required"
                />
              </div>
              <small v-if="error.image" class="text-red-500 text-left">{{
                error.image
              }}</small>
              <div class="col-span-6">
                <label
                  for="callback"
                  class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                  >Callback</label
                >
                <input
                  type="text"
                  name="callback"
                  id="callback"
                  v-model="formData.callback"
                  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  placeholder="http://localhost/api/callback-test"
                  required="required"
                />
              </div>
              <small v-if="error.callback" class="text-red-500 text-left">{{
                error.callback
              }}</small>
            </div>
          </div>
          <!-- <div class="text-red-500 text-center">{{ error }}</div> -->
        </slot>
        <!-- Modal footer -->
        <slot name="footer">
          <div
            class="flex items-center justify-end p-6 space-x-2 rounded-b border-t border-gray-200 dark:border-gray-600"
          >
            <button
              @click="sendReportImage()"
              class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            >
              Send
            </button>
          </div>
        </slot>
      </div>
    </div>
  </div>
</template>

<script>
import { mapMutations } from "vuex";
export default {
  name: "AddNewModal",
  props: {
    title: String,
  },
  data() {
    return {
      formData: {
        user_id: null,
        image: null,
        callback: null,
      },
      error: {
        user_id: null,
        image: null,
        callback: null,
      },
    };
  },
  setup() {
    return {};
  },
  methods: {
    ...mapMutations({
      updateLoader: "UPDATE_LOADER",
    }),
    fileChange() {
      this.formData.image = this.$refs.file.files[0];
    },
    sendReportImage() {
      this.updateLoader(true);
      const fd = new FormData();
      fd.append("image", this.formData.image);
      fd.append("user_id", this.formData.user_id);
      fd.append("callback", this.formData.callback);
      this.$store
        .dispatch("reportImage", fd)
        .then((response) => {
          this.$store.commit("ADD_REPORT", response.data.data);
          this.$emit("close");
          this.error = {
            user_id: null,
            image: null,
            callback: null,
          };
          this.updateLoader(false);
        })
        .catch((err) => {
          if (err.response.data) {
            const error = err.response.data.data;
            if (error.user_id) this.error.user_id = error.user_id[0];
            if (error.image) this.error.image = error.image[0];
            if (error.callback) this.error.callback = error.callback[0];
          }
          // this.error = "Something when wrong";
          this.updateLoader(false);
        });
    },
  },
};
</script>

<style lang="scss" scoped></style>
