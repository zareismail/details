Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'details',
      path: '/details',
      component: require('./components/Tool'),
    },
  ])
})
