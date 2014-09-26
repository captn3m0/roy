require "slack_util"

class SlackUtilTest < ActiveSupport::TestCase
  def setup
    @slack = SlackUtil.new "Invalid Token"
    @text = "amon Hello #test <#C02G4EZQB> <#C02G7EZQB> <@U02G4EZPX> <!channel> <!everyone> <!group> @mention !Fake"
  end
  test 'get_channels should work' do
    channels = ["C02G4EZQB", "C02G7EZQB"]
    assert_equal channels, @slack.get_channels(@text)
  end

  test 'get_users should work' do
    users = ["U02G4EZPX"]
    assert_equal users, @slack.get_users(@text)
  end
end