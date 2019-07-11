const getters = {
  sidebar: state => state.app.sidebar,
  device: state => state.app.device,
  token: state => state.user.token,
  user: state => state.user,
  permissionRoutes: state => state.permission.routes,
  errorLogs: state => state.errorLog.logs
}
export default getters
