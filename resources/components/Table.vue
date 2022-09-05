<template>
  <div class="">
    <div class="w-full text-center font-bold text-lg">Report List</div>
    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
      <div class="flex justify-between items-center py-4 bg-white dark:bg-gray-800">
        <div>
          <button
            id="dropdownActionButton"
            data-dropdown-toggle="dropdownAction"
            class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
            type="button"
          >
            <span class="sr-only">Action button</span>
            Action
            <svg
              class="ml-2 w-3 h-3"
              aria-hidden="true"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
              ></path>
            </svg>
          </button>
          <!-- Dropdown menu -->
          <div
            id="dropdownAction"
            class="z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600 hidden"
            data-popper-reference-hidden=""
            data-popper-escaped=""
            data-popper-placement="top"
            style="
              position: absolute;
              inset: auto auto 0px 0px;
              margin: 0px;
              transform: translate(65px, 28px);
            "
          >
            <ul
              class="py-1 text-sm text-gray-700 dark:text-gray-200"
              aria-labelledby="dropdownActionButton"
            >
              <li>
                <a
                  href="#"
                  class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                  >Reward</a
                >
              </li>
              <li>
                <a
                  href="#"
                  class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                  >Promote</a
                >
              </li>
              <li>
                <a
                  href="#"
                  class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                  >Activate account</a
                >
              </li>
            </ul>
            <div class="py-1">
              <a
                href="#"
                class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white"
                >Delete User</a
              >
            </div>
          </div>
        </div>
        <label for="table-search" class="sr-only">Search</label>
        <div class="relative">
          <div
            class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none"
          >
            <svg
              class="w-5 h-5 text-gray-500 dark:text-gray-400"
              aria-hidden="true"
              fill="currentColor"
              viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                fill-rule="evenodd"
                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                clip-rule="evenodd"
              ></path>
            </svg>
          </div>
          <input
            type="text"
            id="table-search-users"
            class="block p-2 pl-10 w-80 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="Search for report"
          />
        </div>
      </div>
      <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead
          class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
        >
          <tr>
            <th scope="col" class="p-4">
              <div class="flex items-center">
                <input
                  id="checkbox-all-search"
                  type="checkbox"
                  class="w-4 h-4 text-blue-600 bg-gray-100 rounded border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                />
                <label for="checkbox-all-search" class="sr-only">checkbox</label>
              </div>
            </th>
            <th scope="col" class="py-3 px-6">Image</th>
            <th scope="col" class="py-3 px-6">probability</th>
            <th scope="col" class="py-3 px-6">Adult</th>
            <th scope="col" class="py-3 px-6">Spoof</th>
            <th scope="col" class="py-3 px-6">medical</th>
            <th scope="col" class="py-3 px-6">violence</th>
            <th scope="col" class="py-3 px-6">racy</th>
            <th scope="col" class="py-3 px-6">evaluated</th>
            <th scope="col" class="py-3 px-6">approved</th>
            <th scope="col" class="py-3 px-6">Action</th>
          </tr>
        </thead>
        <tbody v-if="getAllReports.length > 0">
          <tr
            v-for="report in getAllReports"
            :key="report.id"
            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
          >
            <td class="p-4 w-4">
              <div class="flex items-center">
                <input
                  id="checkbox-table-search-1"
                  type="checkbox"
                  class="w-4 h-4 text-blue-600 bg-gray-100 rounded border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                />
                <label for="checkbox-table-search-1" class="sr-only">checkbox</label>
              </div>
            </td>
            <th
              scope="row"
              class="flex items-center py-4 px-6 text-gray-900 whitespace-nowrap dark:text-white"
            >
              <img class="w-10 h-10 rounded-full" :src="report.image" alt="image" />
              <div class="pl-3">
                <!-- <div class="text-base font-semibold">Neil Sims</div> -->
                <div class="font-normal text-gray-500">UserId: {{ report.user_id }}</div>
              </div>
            </th>
            <td class="py-4 px-6">
              {{ report.probability }}
            </td>
            <td class="py-4 px-6">
              {{ report.adult }}
            </td>
            <td class="py-4 px-6">
              {{ report.spoof }}
            </td>
            <td class="py-4 px-6">
              {{ report.medical }}
            </td>
            <td class="py-4 px-6">
              {{ report.violence }}
            </td>
            <td class="py-4 px-6">
              {{ report.racy }}
            </td>
            <td class="py-4 px-6">
              <div class="flex items-center">
                <div
                  class="h-2.5 w-2.5 rounded-full mr-2"
                  :class="
                    report.evaluated == 1
                      ? 'bg-green-400'
                      : report.evaluated == 0
                      ? 'bg-red-400'
                      : 'bg-gray-400'
                  "
                ></div>
              </div>
            </td>
            <td class="py-4 px-6">
              <div class="flex items-center">
                <span
                  class="px-3 py-2 text-white rounded-full mr-2"
                  :class="
                    report.approved == 1
                      ? 'bg-green-400'
                      : report.approved == 0
                      ? 'bg-red-400'
                      : 'bg-gray-400'
                  "
                  >{{
                    report.approved == 1
                      ? "Approved"
                      : report.approved == 0
                      ? "Rejected"
                      : "pending"
                  }}</span
                >
              </div>
            </td>
            <td class="py-4 px-6 flex flex-row space-x-1">
              <!-- Modal toggle -->
              <!-- <a
              href="#"
              type="button"
              data-modal-toggle="editUserModal"
              class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
              >Archive</a
            > -->
              <span
                @click="(showAproveModal = true), (selectedReport = report)"
                title="Approve or reject"
                class="cursor-pointer"
              >
                <svg
                  class="w-6 h-6"
                  version="1.1"
                  xmlns="http://www.w3.org/2000/svg"
                  xmlns:xlink="http://www.w3.org/1999/xlink"
                  x="0px"
                  y="0px"
                  viewBox="0 0 1000 1000"
                  enable-background="new 0 0 1000 1000"
                  xml:space="preserve"
                >
                  <metadata>
                    Svg Vector Icons : http://www.onlinewebfonts.com/icon
                  </metadata>
                  <g>
                    <g>
                      <path
                        d="M981.2,247.5L508.7,720c-11.9,11.8-31.1,11.8-43,0l-9.5-9.5l0,0l-10.3-10.3l-44.6-44.6l-0.6-0.9L196.4,448.7c-11.8-11.8-11.8-31.1,0-43l64.4-64.3c11.9-11.9,31.1-11.9,43,0l183.8,185.1l386.3-386.4c11.9-11.9,31.1-11.9,43,0l64.5,64.5C992.9,216.4,992.9,235.7,981.2,247.5z"
                      />
                      <path
                        d="M776.8,805.6c0,34.2-27.9,62.1-62.2,62.1H134.4c-34.3,0-62.2-27.9-62.2-62.1V194.4c0-34.2,27.9-62.1,62.2-62.1h580.2c34.3,0,62.2,27.9,62.2,62.1l0,0l44.2-40.9c7.1-50.3-43.7-83.3-106.4-83.3H134.4C65.7,70.1,10,125.7,10,194.4v611.2c0,68.6,55.7,124.3,124.4,124.3h580.2c68.7,0,124.4-55.7,124.4-124.3V441.2l-62.2,67.7L776.8,805.6L776.8,805.6z"
                      />
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                    </g>
                    <g></g>
                    <g></g>
                    <g></g>
                    <g></g>
                    <g></g>
                    <g></g>
                    <g></g>
                    <g></g>
                    <g></g>
                    <g></g>
                    <g></g>
                    <g></g>
                    <g></g>
                    <g></g>
                    <g></g>
                  </g>
                </svg>
              </span>
              <span
                @click="(showArchiveModal = true), (selectedReport = report)"
                title="Archive"
                class="cursor-pointer"
              >
                <svg
                  class="w-6 h-6"
                  version="1.1"
                  id="Capa_1"
                  xmlns="http://www.w3.org/2000/svg"
                  xmlns:xlink="http://www.w3.org/1999/xlink"
                  x="0px"
                  y="0px"
                  width="973.199px"
                  height="973.199px"
                  viewBox="0 0 973.199 973.199"
                  style="enable-background: new 0 0 973.199 973.199"
                  xml:space="preserve"
                >
                  <g>
                    <path
                      d="M924,828.75V315.649c-3.6,0.3-7.199,0.5-10.801,0.5H60c-3.6,0-7.3-0.2-10.8-0.5V828.75c0,33.1,26.9,60,60,60H864
    		C897.1,888.75,924,861.949,924,828.75z M687,513.35c0,16.6-13.4,30-30,30H316.2c-16.6,0-30-13.4-30-30v-79.6
    		c0-16.601,13.4-30,30-30H656.9c16.6,0,30,13.399,30,30v79.6H687z"
                    />
                    <path
                      d="M973.199,196.149v-51.7c0-33.1-26.898-60-60-60H60c-33.1,0-60,26.9-60,60v51.7c0,29.4,21.2,53.9,49.2,59
    		c3.5,0.6,7.1,1,10.8,1h853.1c3.701,0,7.301-0.4,10.801-1C952,250.049,973.199,225.549,973.199,196.149z"
                    />
                  </g>
                  <g></g>
                  <g></g>
                  <g></g>
                  <g></g>
                  <g></g>
                  <g></g>
                  <g></g>
                  <g></g>
                  <g></g>
                  <g></g>
                  <g></g>
                  <g></g>
                  <g></g>
                  <g></g>
                  <g></g>
                </svg>
              </span>
              <span class="h-9"></span>
            </td>
          </tr>
        </tbody>
      </table>
      <!-- modal -->
      <Modal
        v-show="showAproveModal && selectedReport"
        @close="showAproveModal = false"
        :title="'Approve report'"
      >
        <template #body>
          <div class="w-full text-center text-red-400">
            {{ error }}
          </div>
        </template>
        <template #footer>
          <div
            class="flex justify-center items-center p-6 space-x-2 rounded-b border-t border-gray-200 dark:border-gray-600"
          >
            <LoadingButton
              @submit="(isLoadingReject = true), approveReport(selectedReport.id, false)"
              :isLoading="isLoadingReject"
              class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            >
              Reject
            </LoadingButton>
            <LoadingButton
              @submit="(isLoadingApprove = true), approveReport(selectedReport.id, true)"
              :isLoading="isLoadingApprove"
              class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            >
              Approve
            </LoadingButton>
          </div>
        </template>
      </Modal>
      <Modal
        v-show="showArchiveModal && selectedReport"
        @close="showArchiveModal = false"
        :title="'Archive report ?'"
      >
        <template #body>
          <div class="w-full text-center text-red-400">
            {{ error }}
          </div>
        </template>
        <template #footer>
          <div
            class="flex justify-center items-center p-6 space-x-2 rounded-b border-t border-gray-200 dark:border-gray-600"
          >
            <LoadingButton
              @submit="showArchiveModal = false"
              :isLoading="isLoadingReject"
              class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            >
              Cancel
            </LoadingButton>
            <LoadingButton
              @submit="(isLoadingApprove = true), archiveReport(selectedReport.id)"
              :isLoading="isLoadingApprove"
              class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            >
              Archive
            </LoadingButton>
          </div>
        </template>
      </Modal>
    </div>
  </div>
</template>

<script>
import Modal from "./Modal.vue";
import LoadingButton from "./LoadingButton.vue";
export default {
  name: "Table",
  props: {
    reports: Array,
  },
  setup() {
    return {};
  },
  data() {
    return {
      selectedReport: null,
      showAproveModal: false,
      showArchiveModal: false,
      isLoadingApprove: false,
      isLoadingReject: false,
      error: "",
    };
  },
  components: {
    Modal,
    LoadingButton,
  },
  computed: {
    getAllReports() {
      //final output from here
      return this.$store.getters.getReportsFormGetters;
    },
  },
  methods: {
    archiveReport(id) {
      this.$store
        .dispatch("archiveReport", id)
        .then((response) => {
          this.showArchiveModal = false;
          this.error = "";
          this.isLoadingApprove = false;
          this.isLoadingReject = false;
        })
        .catch((err) => {
          this.error = "Something when wrong";
        });
    },
    async approveReport(id, approve) {
      this.$store
        .dispatch("approveReport", { id, approve })
        .then((response) => {
          this.showAproveModal = false;
          this.error = "";
          this.isLoadingApprove = false;
          this.isLoadingReject = false;
        })
        .catch((err) => {
          this.error = "Something when wrong";
        });
    },
  },
};
</script>

<style lang="scss" scoped></style>
