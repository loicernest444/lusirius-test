
export default {

	state: {
       reports: [],
       isLoading: false
	},

	getters: {
       getReportsFormGetters(state){ //take parameter state
          return state.reports
       },
       isLoading: state => state.isLoading
	},

	actions: {
       async getAllreports(context){
              return await axios.get("/api/reports").then((response) => {
                context.commit("SET_REPORTS",response.data.data) //reports will be run from mutation
              })
              .catch((err)=>{
                 console.log("Error........", err)
              })
       },
       async approveReport(context, payload){
        const id = payload.id
        const approve = payload.approve
        return await axios
        .put(`/api/approve-report/${id}`, { approve: approve })
        .then((response) => {
            context.commit("UPDATE_REPORT", response.data.data)
        })
        // .catch((err) => {
        //   return err
        // });
       },
       async archiveReport(context, id){
        return await axios
        .delete(`/api/archive-image-report/${id}`)
        .then((response) => {
            context.commit("REMOVE_REPORT", id)
        })
        // .catch((err) => {
        //   return err
        // });
       },
       async reevaluateReport({ commit }, id){
        return await axios
        .get(`/api/reevaluate-report/${id}`)
        .then((response) => {
            commit("UPDATE_REPORT", response.data.data)
            // return response
        })
        // .catch((err) => {
        //   return err
        // });
       },
       async reportImage({ commit }, payload){
        return await axios
        .post(`/api/report-image`, payload)
        // .then((response) => {
        //     context.commit("ADD_REPORT", response.data.data)
        //     return response
        // })
        // .catch((err) => {
        //   return err
        // });
       }
	},

	mutations: {
        UPDATE_LOADER(state, value){
            state.isLoading = value
        },
       SET_REPORTS(state, data) {
          return state.reports = data
       },
       REMOVE_REPORT(state, $id) {
        const index = state.reports.findIndex((r) => r.id == $id);
        if (index >= 0) {
            state.reports.splice(index, 1);
        }
      },
       UPDATE_REPORT(state, $report) {
        const index = state.reports.findIndex((r) => r.id == $report.id);
        if (index >= 0) {
            state.reports.splice(index, 1, $report);
        }
      },
      SORT_REPORTS(state){
        state.reports = state.reports.sort((a,b) => b.probability_level - a.probability_level)
      },
      ADD_REPORT(state, report){
        state.reports.unshift(report)
      }
	}
}