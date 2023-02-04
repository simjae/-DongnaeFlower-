<style>
.order_wrap .order_group{background:#f4f4f4;padding:20px;margin-top:8px;border-radius:4px;}
.order_wrap h2{padding-bottom:10px;margin-bottom:10px;border-bottom:solid 1px #e0e0e0;}
.bg_wrap {
  background: linear-gradient(to bottom, #d3dd6d 0%, #6eb966 100%);
  height:100vh;
}

#sky {
  width: 100vw;
  height: 100vh;
  position: fixed;
  overflow: hidden;
  margin: 0;
  padding: 0;
}

#shootingstars {
  margin: 0;
  padding: 0;
  width: 150vh;
  height: 100vw;
  position: fixed;
  overflow: hidden;
  transform: translatex(calc(50vw - 50%)) translatey(calc(50vh - 50%))
    rotate(120deg);
}

.wish {
  height: 2px;
  top: 300px;
  width: 100px;
  margin: 0;
  opacity: 0;
  padding: 0;
  background-color: white;
  position: absolute;
  background: linear-gradient(-45deg, white, rgba(0, 0, 255, 0));
  filter: drop-shadow(0 0 6px white);
  overflow: hidden;
}

</style>

<script>
function _defineProperty(obj, key, value) {if (key in obj) {Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true });} else {obj[key] = value;}return obj;} // Twinkling Night Sky by Sharna

$(document).ready(function(){
	$("#gotop").hide();
});
</script>
<div id="content">
	<!-- 주문결제 -->
	<input type="hidden" name="msg_type" value="1" />
	<input type="hidden" name="addorder_msg" value="" />
	<input type="hidden" name="sumprice" value="<?=$basketItems['sumprice']?>" />

	<div class="bg_wrap">
		<div id="root">
		</div>
		<div style="position:absolute;top:50%;left:50%;width:260px;margin-left:-130px;text-align:center;color:#FFFFFF;font-weight:200;">
			<img src="/app/skin/basic/svg/logo.svg"  style="height: 60px;margin-bottom:20px">
			<br>로그인 처리중입니다.
			<br>잠시만 기다려주세요
		</div>
		<div style="position:absolute;bottom:15px;left:15px;color:#FFFFFF;font-weight:100;">

		</div>
	</div>
	<!-- 주문정보 END -->

	<Script>
		$(document).ready(function() {
			ReactDOM.render( /*#__PURE__*/React.createElement(StarrySky, null), document.getElementById("root"));

		});
	</script>

	<script type="text/javascript">

class StarrySky extends React.Component {constructor(...args) {super(...args);_defineProperty(this, "state",
    {
      num: 60,
      vw: Math.max(document.documentElement.clientWidth, window.innerWidth || 0),
      vh: Math.max(document.documentElement.clientHeight, window.innerHeight || 0) });_defineProperty(this, "starryNight",

    () => {
      anime({
        targets: ["#sky .star"],
        opacity: [
        {
          duration: 700,
          value: "0" },

        {
          duration: 700,
          value: "1" }],


        easing: "linear",
        loop: true,
        delay: (el, i) => 50 * i });

    });_defineProperty(this, "shootingStars",
    () => {
      anime({
        targets: ["#shootingstars .wish"],
        easing: "linear",
        loop: true,
        delay: (el, i) => 1000 * i,
        opacity: [
        {
          duration: 700,
          value: "1" }],


        width: [
        {
          value: "150px" },

        {
          value: "0px" }],


        translateX: 350 });

    });_defineProperty(this, "randomRadius",
    () => {
      return Math.random() * 0.7 + 0.6;
    });_defineProperty(this, "getRandomX",
    () => {
      return Math.floor(Math.random() * Math.floor(this.state.vw)).toString();
    });_defineProperty(this, "getRandomY",
    () => {
      return Math.floor(Math.random() * Math.floor(this.state.vh)).toString();
    });}
  componentDidMount() {
    this.starryNight();
    this.shootingStars();
  }
  render() {
    const { num } = this.state;
    return /*#__PURE__*/(
      React.createElement("div", { id: "App" }, /*#__PURE__*/
      React.createElement("svg", { id: "sky" },
      [...Array(num)].map((x, y) => /*#__PURE__*/
      React.createElement("circle", {
        cx: this.getRandomX(),
        cy: this.getRandomY(),
        r: this.randomRadius(),
        stroke: "none",
        strokeWidth: "0",
        fill: "white",
        key: y,
        className: "star" }))), /*#__PURE__*/



      React.createElement("div", { id: "shootingstars" },
      [...Array(60)].map((x, y) => /*#__PURE__*/
      React.createElement("div", {
        key: y,
        className: "wish",
        style: {
          left: `${this.getRandomY()}px`,
          top: `${this.getRandomX()}px` } })))));






  }}

	//-->
	</script>
</div>