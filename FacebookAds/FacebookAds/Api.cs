using Facebook;

namespace FacebookAds
{
    /// <summary>
    ///     Api class initializer
    /// </summary>
    public static class Api
    {
        /// <summary>
        ///     The version of the graph API we're using
        /// </summary>
        private const string VERSION = "v7.0";

        /// <summary>
        ///     Initializes the application via tokens
        /// </summary>
        /// <param name="appId">The application identifier.</param>
        /// <param name="appSecret">The application secret.</param>
        /// <param name="accessToken">The access token.</param>
        /// <param name="apiVersion">The API version.</param>
        public static FacebookClient Initialize(string appId, string appSecret, string accessToken,
            string apiVersion = null)
        {
            return new FacebookClient
            {
                AppId = appId,
                AppSecret = appSecret,
                AccessToken = accessToken,
                Version = apiVersion ?? VERSION
            };
        }
    }
}