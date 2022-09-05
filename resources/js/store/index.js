
export default {

	state: {

       reports: []

	},

	getters: {
       getReportsFormGetters(state){ //take parameter state
          return state.reports
       }
	},

	actions: {
       async getAllreports(context){
               await axios.get("/api/reports").then((response) => {
                context.commit("SET_REPORTS",response.data.data) //reports will be run from mutation
              })
              .catch((err)=>{
                 console.log("Error........", err)
              })
       },
       async approveReport(context, payload){
        const id = payload.id
        const approve = payload.approve
        await axios
        .put(`/api/approve-report/${id}`, { approve: approve })
        .then((response) => {
            context.commit("UPDATE_REPORT", response.data.data)
            return response
        })
        .catch((err) => {
          this.error = "Something when wrong";
          return err
        });
       },
       async archiveReport(context, id){
        await axios
        .delete(`/api/archive-image-report/${id}`)
        .then((response) => {
            context.commit("REMOVE_REPORT", id)
            return response
        })
        .catch((err) => {
          this.error = "Something when wrong";
          return err
        });
       }
	},

	mutations: {
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
      }
	}
}