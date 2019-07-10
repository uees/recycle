import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

/* Layout */
import Layout from '@/layout'

/**
 * Note: sub-menu only appear when route children.length >= 1
 * Detail see: https://panjiachen.github.io/vue-element-admin-site/guide/essentials/router-and-nav.html
 *
 * hidden: true                   if set true, item will not show in the sidebar(default is false)
 * alwaysShow: true               if set true, will always show the root menu
 *                                if not set alwaysShow, when item has more than one children route,
 *                                it will becomes nested mode, otherwise not show the root menu
 * redirect: noRedirect           if set noRedirect will no redirect in the breadcrumb
 * name:'router-name'             the name is used by <keep-alive> (must set!!!)
 * meta : {
    roles: ['admin','editor']    control the page roles (you can set multiple roles)
    title: 'title'               the name show in sidebar and breadcrumb (recommend set)
    icon: 'svg-name'             the icon show in the sidebar
    breadcrumb: false            if set false, the item will hidden in breadcrumb(default is true)
    activeMenu: '/example/list'  if set path, the sidebar will highlight the path you set
  }
 */

/**
 * constantRoutes
 * a base page that does not have permission requirements
 * all roles can be accessed
 */
export const constantRoutes = [
  {
    path: '/login',
    component: () => import('@/views/login/index'),
    hidden: true
  },

  {
    path: '/404',
    component: () => import('@/views/404'),
    hidden: true
  },

  {
    path: '/',
    component: Layout,
    redirect: '/dashboard',
    children: [{
      path: 'dashboard',
      name: 'Dashboard',
      component: () => import('@/views/dashboard/index'),
      meta: { title: '主页', icon: 'dashboard' }
    }]
  },

  {
    path: '/entering-warehouses',
    component: Layout,
    children: [{
      path: 'index',
      name: 'EnteringWarehouses',
      component: () => import('@/views/entering_warehouses/index'),
      meta: { title: '入库管理', icon: 'table' }
    }]
  },

  {
    path: '/shipments',
    component: Layout,
    children: [{
      path: 'index',
      name: 'Shipments',
      component: () => import('@/views/shipments/index'),
      meta: { title: '发货管理', icon: 'table' }
    }]
  },

  {
    path: '/recycled-things',
    component: Layout,
    children: [{
      path: 'index',
      name: 'RecycledThings',
      component: () => import('@/views/dashboard/index'),
      meta: { title: '回收管理', icon: 'table' }
    }]
  },

  {
    path: '/basedata',
    component: Layout,
    redirect: '/basedata/menu1',
    name: 'Basedata',
    meta: {
      title: '系统设置',
      icon: 'nested'
    },
    children: [
      {
        path: 'members',
        component: () => import('@/views/members/index'), // Parent router-view
        name: 'Members',
        meta: { title: '用户管理' },
        children: [
          {
            path: 'users',
            component: () => import('@/views/members/Users'),
            name: 'Users',
            meta: { title: '用户' }
          },
          {
            path: 'roles',
            component: () => import('@/views/members/Roles'),
            name: 'Roles',
            meta: { title: '角色' }
          }
        ]
      },
      {
        path: 'customers',
        component: () => import('@/views/customers/index'),
        name: 'Customers',
        meta: { title: '客户' }
      }
    ]
  },

  // 404 page must be placed at the end !!!
  { path: '*', redirect: '/404', hidden: true }
]

const createRouter = () => new Router({
  // mode: 'history', // require service support
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRoutes
})

const router = createRouter()

// Detail see: https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465
export function resetRouter() {
  const newRouter = createRouter()
  router.matcher = newRouter.matcher // reset router
}

export default router
